<?php

namespace App\Controller;

use App\Entity\Department;
use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/employee')]
class EmployeeController extends AbstractController
{
    #[Route('/', name: 'app_employee_index', methods: ['GET'])]
    public function index(EmployeeRepository $employeeRepository): JsonResponse
    {
        $employees = $employeeRepository->findAll();

        if (!$employees)
        {
            return new JsonResponse(['error' => 'Ops'], Response::HTTP_NOT_FOUND);
        }

        $data = [];
        foreach ($employees as $employee)
        {
            $data[] = [
                'id' => $employee->getId(),
                'name' => $employee->getName(),
                'phone' => $employee->getPhone(),
                'department' => $employee->getDepartmentId(),
                'dependents' => $employee->getDependents(),
                'document' => $employee->getDocument(),
            ];
        }
        return new JsonResponse(['employees' => $data], Response::HTTP_OK);
    }

    #[Route('/create', name: 'app_employee_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $requiredFields = ['name', 'document', 'department_id', 'dependents', 'phone'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                return new JsonResponse(['error' => 'Missing required fields'], Response::HTTP_BAD_REQUEST);
            }
        }

        $departmentId = $data['department_id'];
        $department = $entityManager->getRepository(Department::class)->find($departmentId);
        if (!$department) {
            return new JsonResponse(['error' => 'Department not found'], Response::HTTP_NOT_FOUND);
        }

        $employee = new Employee();
        $employee->setName($data['name']);
        $employee->setDocument($data['document']);
        $employee->setDepartmentId($department);
        $employee->setDependents($data['dependents']);
        $employee->setPhone($data['phone']);

        $entityManager->persist($employee);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Employee created successfully'], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'app_employee_show', methods: ['GET'])]
    public function show(EmployeeRepository $employeeRepository, int $id): JsonResponse
    {
        $employee = $employeeRepository->find($id);
        if (!$employee) {
            return new JsonResponse(['error' => 'Ops'], Response::HTTP_NOT_FOUND);
        }
        $data[] = [
            'id' => $employee->getId(),
            'name' => $employee->getName(),
            'phone' => $employee->getPhone(),
            'department' => $employee->getDepartmentId(),
            'dependents' => $employee->getDependents(),
            'document' => $employee->getDocument(),
        ];
        return new JsonResponse(['employee' => $data], Response::HTTP_OK);
    }

    #[Route('/{id}/edit', name: 'app_employee_edit', methods: ['PUT'])]
    public function edit(Request $request, Employee $employee, EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (isset($data['name'])) {
            $employee->setName($data['name']);
        }
        if (isset($data['document'])) {
            $employee->setDocument($data['document']);
        }
        if (isset($data['department_id'])) {
            $departmentId = $data['department_id'];
            $department = $entityManager->getRepository(Department::class)->find($departmentId);
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
        return new JsonResponse(['message' => 'Employee updated'], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'app_employee_delete', methods: ['DELETE'])]
    public function delete(EntityManagerInterface $entityManager, EmployeeRepository $employeeRepository, int $id): JsonResponse
    {
        $employee = $employeeRepository->find($id);

        $entityManager->remove($employee);
        $entityManager->flush();

        return new JsonResponse(['message' => 'Employee deleted'], Response::HTTP_OK);
    }
}
