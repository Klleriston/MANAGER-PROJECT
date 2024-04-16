<?php

namespace App\Test\Controller;

use App\Entity\Employee;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EmployeeControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private EntityManagerInterface $manager;
    private EntityRepository $repository;
    private string $path = '/employee/';

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->manager = static::getContainer()->get('doctrine')->getManager();
        $this->repository = $this->manager->getRepository(Employee::class);

        foreach ($this->repository->findAll() as $object) {
            $this->manager->remove($object);
        }

        $this->manager->flush();
    }

    public function testIndex(): void
    {
        $crawler = $this->client->request('GET', $this->path);

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Employee index');

        // Use the $crawler to perform additional assertions e.g.
        // self::assertSame('Some text on the page', $crawler->filter('.p')->first());
    }

    public function testNew(): void
    {
        $this->markTestIncomplete();
        $this->client->request('GET', sprintf('%snew', $this->path));

        self::assertResponseStatusCodeSame(200);

        $this->client->submitForm('Save', [
            'employee[name]' => 'Testing',
            'employee[dependents]' => 'Testing',
            'employee[document]' => 'Testing',
            'employee[phone]' => 'Testing',
            'employee[department_id]' => 'Testing',
        ]);

        self::assertResponseRedirects($this->path);

        self::assertSame(1, $this->repository->count([]));
    }

    public function testShow(): void
    {
        $this->markTestIncomplete();
        $fixture = new Employee();
        $fixture->setName('My Title');
        $fixture->setDependents('My Title');
        $fixture->setDocument('My Title');
        $fixture->setPhone('My Title');
        $fixture->setDepartment_id('My Title');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));

        self::assertResponseStatusCodeSame(200);
        self::assertPageTitleContains('Employee');

        // Use assertions to check that the properties are properly displayed.
    }

    public function testEdit(): void
    {
        $this->markTestIncomplete();
        $fixture = new Employee();
        $fixture->setName('Value');
        $fixture->setDependents('Value');
        $fixture->setDocument('Value');
        $fixture->setPhone('Value');
        $fixture->setDepartment_id('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s/edit', $this->path, $fixture->getId()));

        $this->client->submitForm('Update', [
            'employee[name]' => 'Something New',
            'employee[dependents]' => 'Something New',
            'employee[document]' => 'Something New',
            'employee[phone]' => 'Something New',
            'employee[department_id]' => 'Something New',
        ]);

        self::assertResponseRedirects('/employee/');

        $fixture = $this->repository->findAll();

        self::assertSame('Something New', $fixture[0]->getName());
        self::assertSame('Something New', $fixture[0]->getDependents());
        self::assertSame('Something New', $fixture[0]->getDocument());
        self::assertSame('Something New', $fixture[0]->getPhone());
        self::assertSame('Something New', $fixture[0]->getDepartment_id());
    }

    public function testRemove(): void
    {
        $this->markTestIncomplete();
        $fixture = new Employee();
        $fixture->setName('Value');
        $fixture->setDependents('Value');
        $fixture->setDocument('Value');
        $fixture->setPhone('Value');
        $fixture->setDepartment_id('Value');

        $this->manager->persist($fixture);
        $this->manager->flush();

        $this->client->request('GET', sprintf('%s%s', $this->path, $fixture->getId()));
        $this->client->submitForm('Delete');

        self::assertResponseRedirects('/employee/');
        self::assertSame(0, $this->repository->count([]));
    }
}
