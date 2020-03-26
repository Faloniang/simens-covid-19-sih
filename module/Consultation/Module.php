<?php

namespace Consultation;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\ServiceProviderInterface;
use Zend\ModuleManager\Feature\ViewHelperProviderInterface;
use Zend\Mvc\MvcEvent;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;
use Consultation\Model\PersonneTable;
use Consultation\Model\Personne;
use Consultation\Model\ConsultationTable;
use Consultation\Model\Consultation;


class Module implements AutoloaderProviderInterface, ConfigProviderInterface, ServiceProviderInterface, ViewHelperProviderInterface {

	public function registerJsonStrategy(MvcEvent $e)
	{
		$app          = $e->getTarget();
		$locator      = $app->getServiceManager();
		$view         = $locator->get('Zend\View\View');
		$jsonStrategy = $locator->get('ViewJsonStrategy');

		// Attach strategy, which is a listener aggregate, at high priority
		$view->getEventManager()->attach($jsonStrategy, 100);
	}

	public function getAutoloaderConfig() {
		return array (
				'Zend\Loader\StandardAutoloader' => array (
						'namespaces' => array (
								__NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__
						)
				)
		);
	}
	
	public function getConfig() {
		return include __DIR__ . '/config/module.config.php';
	}
	
	public function getServiceConfig() {
		return array (
				'factories' => array (
						'Consultation\Model\PersonneTable' => function ($sm) {
						    $tableGateway = $sm->get( 'PersonneConsultationTableGateway' );
						    $table = new PersonneTable( $tableGateway );
						    return $table;
						},
						
						'PersonneConsultationTableGateway' => function ($sm) {
						    $dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
						    $resultSetPrototype = new ResultSet ();
						    $resultSetPrototype->setArrayObjectPrototype ( new Personne() );
						    return new TableGateway ( 'personne', $dbAdapter, null, $resultSetPrototype );
						},
						
						'Consultation\Model\ConsultationTable' => function ($sm) {
    						$tableGateway = $sm->get ( 'ConsultationTableGateway' );
    						$table = new ConsultationTable( $tableGateway );
    						return $table;
						},
						
						'ConsultationTableGateway' => function ($sm) {
    						$dbAdapter = $sm->get ( 'Zend\Db\Adapter\Adapter' );
    						$resultSetPrototype = new ResultSet();
    						$resultSetPrototype->setArrayObjectPrototype ( new Consultation());
    						return new TableGateway ( 'consultation', $dbAdapter, null, $resultSetPrototype );
						},
				)
		);
	}
	
	public function getViewHelperConfig() {
		return array ();
	}
}