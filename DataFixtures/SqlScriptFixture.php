<?php
namespace Core\Bundle\CoreBundle\DataFixtures;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;

abstract class SqlScriptFixture extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{
    protected $container;
    protected $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;
        $this->createFixtures();
        if ($this->get('kernel')->getEnvironment() == 'test') {
            $this->createTestFixtures();
        }
    }

    public function createFixtures() {}
    public function createTestFixtures() {}

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    public function get($service)
    {
        return $this->container->get($service);
    }

    protected function getManager()
    {
        return $this->container->get('doctrine')->getManager();
    }

    protected function getEntityManager()
    {
        return $this->container->get('doctrine')->getManager();
    }

    protected function getRepository($repo)
    {
        return $this->getEntityManager()->getRepository($repo);
    }

    protected function runSqlScript($script)
    {
        $class_info = new \ReflectionClass($this);
        $dir = dirname($class_info->getFileName());

        $f = file($dir . DIRECTORY_SEPARATOR . "sql" . DIRECTORY_SEPARATOR . $script);
        $request = "";
        $dba = $this->container->get('database_connection');

        foreach ($f as $num_line => $line) {
            $request = $request ." ". rtrim($line);

            if (substr(rtrim($line), -1) == ';') {
                $dba->query($request);
                $request = "";
            }
        }
    }
}
