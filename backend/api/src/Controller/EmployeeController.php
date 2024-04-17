<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Util\Json;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class EmployeeController extends AbstractController
{
    #[Route('/employee', name: 'app_employee_index', methods: ['GET'])]
    public function index(EmployeeRepository $employeeRepository): JsonResponse
    {
        $employees = $employeeRepository->findAll();
        if (!$employees) {
            return new JsonResponse(['error' => 'Ops'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(['employees' => $employees], Response::HTTP_OK);
    }

    #[Route('/employee/create', name: 'app_employee_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

        $requiredFields = ['name', 'document', 'department_id', 'dependents', 'phone'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
            }
        }

        $employee = new Employee();

        $departmentId = $data['department_id'];
        $department = $entityManager->getRepository(Department::class)->find($departmentId);
        if (!$department) {
            return new JsonResponse(['error' => 'Department not found'], Response::HTTP_NOT_FOUND);
        }

        $employee->setName($data['name']);
        $employee->setDocument($data['document']);
        $employee->setDepartmentId($department);
        $employee->setDependents($data['dependents']);
        $employee->setPhone($data['phone']);

        $entityManager->persist($employee);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Employee created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/employee/{id}', name: 'app_employee_show', methods: ['GET'])]
    public function show(EmployeeRepository $employeeRepository, int $id): Response
    {
        $employee = $employeeRepository->find($id);
        if (!$employee) {
            return new JsonResponse(['error' => 'Ops'], Response::HTTP_NOT_FOUND);
        }
        return new JsonResponse(['employees' => $employee], Response::HTTP_OK);
    }

    #[Route('/employee/{id}/edit', name: 'app_employee_edit', methods: ['PATCH', 'PUT'])]
    public function edit(Request $request, Employee $employee, EntityManagerInterface $entityManager, int $id): Response
    {
        $data = json_decode($request->getContent(), true);

        $employee = $entityManager->getRepository(Employee::class)->find($id);

        if (!$employee) {
            return new JsonResponse(['error' => 'Ops'], Response::HTTP_NOT_FOUND);
        }

        if (isset($data['name'])) {
            $employee->setName($data['name']);
        }
        if (isset($data['document'])) {
            $employee->setDocument($data['document']);
        }
        if (isset($data['department_id'])) {
            $department = $entityManager->getRepository(Department::class)->find($data['department_id']);
            if (!$department) {
                return new JsonResponse(['error' => 'Department not found'], Response::HTTP_NOT_FOUND);
            }
            $employee->setDepartmentId($department);
        }
        if (isset($data['dependents'])) {
            $employee->setDependents($data['dependents']);
        }
        if (isset($data['phone'])) {
            $employee->setPhone($data['phone']);
        }
        $entityManager->flush();
        return new JsonResponse(['Message' => 'Employee updated !'], Response::HTTP_OK);
    }

    #[Route('/employee/{id}', name: 'app_employee_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, int $id): Response
    {
       $employee = $entityManager->getRepository(Employee::class)->find($id);
        if (!$employee) {
            return new JsonResponse(['error' => 'Ops'], Response::HTTP_NOT_FOUND);
        }

        $entityManager->remove($employee);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Employee deleted'], Response::HTTP_OK);

    }
}
