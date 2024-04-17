<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\Employee;
use App\Form\DepartmentType;
use App\Repository\DepartmentRepository;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/department')]
class DepartmentController extends AbstractController
{
    #[Route('/', name: 'app_department_index', methods: ['GET'])]
    public function index(DepartmentRepository $departmentRepository): JsonResponse
    {
        $departments = $departmentRepository->findAll();
        if (!$departments) {
            return new JsonResponse(['error' => 'No departments found'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(['departments' => $departments], Response::HTTP_OK);
    }

    #[Route('/create', name: 'app_department_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $requiredFields = ['title', 'acronym'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
            }
        }

        $department = new Department();
        $department->setTitle($data['title']);
        $department->setAcronym($data['acronym']);

        $entityManager->persist($department);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Department created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_department_show', methods: ['GET'])]
    public function show(Department $department): JsonResponse
    {
        $employees = $department->getEmployees()->toArray();
        return new JsonResponse(['employees' => $employees], Response::HTTP_OK);
    }

    #[Route('/{id}/edit', name: 'app_department_edit', methods: ['PUT'])]
    public function edit(Request $request, Department $department, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['title'])) {
            $department->setTitle($data['title']);
        }
        if (isset($data['acronym'])) {
            $department->setAcronym($data['acronym']);
        }

        $entityManager->flush();
        return new JsonResponse(['message' => 'Department updated'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_department_delete', methods: ['DELETE'])]
    public function delete(Department $department, EntityManagerInterface $entityManager): JsonResponse
    {
        $entityManager->remove($department);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Department deleted'], Response::HTTP_OK);
    }
}
