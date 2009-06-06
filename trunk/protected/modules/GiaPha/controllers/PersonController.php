<?php

class PersonController extends CController
{
	const PAGE_SIZE=10;

	/**
	 * @var string specifies the default action to be 'list'.
	 */
	public $defaultAction='list';

	/**
	 * @var CActiveRecord the currently loaded data model instance.
	 */
	private $_person;

	/**
	 * @return array action filters
	 */
	public function filters()
	{
		return array(
			'accessControl', // perform access control for CRUD operations
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
	{
		return array(
			array('allow',  // allow all users to perform 'list' and 'show' actions
				'actions'=>array('list','show'),
				'users'=>array('*'),
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('create','update'),
				'users'=>array('@'),
			),
			array('allow', // allow admin user to perform 'admin' and 'delete' actions
				'actions'=>array('admin','delete'),
				'users'=>array('admin'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

	/**
	 * Shows a particular person.
	 */
	public function actionShow()
	{
		$this->render('show',array('person'=>$this->loadPerson()));
	}

	/**
	 * Creates a new person.
	 * If creation is successful, the browser will be redirected to the 'show' page.
	 */
	public function actionCreate()
	{
		$person=new Person;
		if(isset($_POST['Person']))
		{
			$person->attributes=$_POST['Person'];
			if($person->save())
				$this->redirect(array('show','id'=>$person->id));
		}
		$this->render('create',array('person'=>$person));
	}

	/**
	 * Updates a particular person.
	 * If update is successful, the browser will be redirected to the 'show' page.
	 */
	public function actionUpdate()
	{
		$person=$this->loadPerson();
		if(isset($_POST['Person']))
		{
			$person->attributes=$_POST['Person'];
			if($person->save())
				$this->redirect(array('show','id'=>$person->id));
		}
		$this->render('update',array('person'=>$person));
	}

	/**
	 * Deletes a particular person.
	 * If deletion is successful, the browser will be redirected to the 'list' page.
	 */
	public function actionDelete()
	{
		if(Yii::app()->request->isPostRequest)
		{
			// we only allow deletion via POST request
			$this->loadPerson()->delete();
			$this->redirect(array('list'));
		}
		else
			throw new CHttpException(500,'Invalid request. Please do not repeat this request again.');
	}

	/**
	 * Lists all persons.
	 */
	public function actionList()
	{
		$criteria=new CDbCriteria;

		$pages=new CPagination(Person::model()->count($criteria));
		$pages->pageSize=self::PAGE_SIZE;
		$pages->applyLimit($criteria);

		$personList=Person::model()->findAll($criteria);

		$this->render('list',array(
			'personList'=>$personList,
			'pages'=>$pages,
		));
	}

	/**
	 * Manages all persons.
	 */
	public function actionAdmin()
	{
		$this->processAdminCommand();

		$criteria=new CDbCriteria;

		$pages=new CPagination(Person::model()->count($criteria));
		$pages->pageSize=self::PAGE_SIZE;
		$pages->applyLimit($criteria);

		$sort=new CSort('Person');
		$sort->applyOrder($criteria);

		$personList=Person::model()->findAll($criteria);

		$this->render('admin',array(
			'personList'=>$personList,
			'pages'=>$pages,
			'sort'=>$sort,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the primary key value. Defaults to null, meaning using the 'id' GET variable
	 */
	public function loadPerson($id=null)
	{
		if($this->_person===null)
		{
			if($id!==null || isset($_GET['id']))
				$this->_person=Person::model()->findbyPk($id!==null ? $id : $_GET['id']);
			if($this->_person===null)
				throw new CHttpException(500,'The requested person does not exist.');
		}
		return $this->_person;
	}

	/**
	 * Executes any command triggered on the admin page.
	 */
	protected function processAdminCommand()
	{
		if(isset($_POST['command'], $_POST['id']) && $_POST['command']==='delete')
		{
			$this->loadPerson($_POST['id'])->delete();
			// reload the current page to avoid duplicated delete actions
			$this->refresh();
		}
	}
}
