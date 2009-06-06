<?php

class Tree extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @return CActiveRecord the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'Tree';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('name','length','max'=>255),
			array('lft, rgt, level', 'required'),
			array('lft, rgt, level', 'numerical', 'integerOnly'=>true),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id'=>'Id',
			'lft'=>'Lft',
			'rgt'=>'Rgt',
			'level'=>'Level',
			'name'=>'Name',
		);
	}
	
	public function behaviors(){
        return array(
            'TreeBehavior' => array(
                'class' => 'application.extensions.nestedset.TreeBehavior'
            )
        );
    }  
    
}