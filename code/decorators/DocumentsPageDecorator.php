<?php
/**
 * Example implementation:

DocumentsPageDecorator::init();
DataObject::add_extension('DocumentsPage', 'DocumentsPageDecorator');

class DocumentsPage extends Page {}
class DocumentsPage_Controller extends Page_Controller {}

 */

class DocumentsPageDecorator extends SiteTreeDecorator {

	static function init() {
		SortableDataObject::add_sortable_classes(array('DocumentsPageDecorator_Category', 'DocumentsPageDecorator_Document'));
	} 

	function extraStatics() {
		return array(
			'has_many' => array(
				'Categories' => 'DocumentsPageDecorator_Category',
				'Documents' => 'DocumentsPageDecorator_Document',
			)
		);
	}

	function updateCMSFields( $fields ) {
		$fields->addFieldToTab('Root.Content.Documents', $field = new DataObjectManager(
			$this->owner, // controller
			'Categories', // name
			'DocumentsPageDecorator_Category' // sourceClass
		));
		$fields->addFieldToTab('Root.Content.Documents', $field = new DataObjectManager(
			$this->owner, // controller
			'Documents', // name
			'DocumentsPageDecorator_Document' // sourceClass
		));
	}

}

class DocumentsPageDecorator_Category extends DataObject {

	static $db = array(
		'Title' => 'Varchar(255)',
	);

	static $has_one = array(
		'Page' => 'Page',
	);

	static $has_many = array(
		'Documents' => 'DocumentsPageDecorator_Document',
	);

	static $singular_name = 'Category';

	function getCMSFields() {
		$fields = FormUtils::createMain();
		$fields->addFieldToTab('Root.Main', $field = new TextField('Title'));
		return $fields;
	}

}

class DocumentsPageDecorator_Document extends DataObject {

	static $db = array(
		'Title' => 'Varchar(255)',
		'Description' => 'HTMLText',
	);

	static $has_one = array(
		'Page' => 'Page',
		'Document' => 'File',
		'Category' => 'DocumentsPageDecorator_Category',
	);

	static $singular_name = 'Document';

	function getCMSFields() {
		$fields = FormUtils::createMain();
		$fields->addFieldToTab('Root.Main', $field = new TextField('Title'));
		$fields->addFieldToTab('Root.Main', $field = new SimpleTinyMCEField('Description'));
		$fields->addFieldToTab('Root.Main', $field = new FileUploadField('File'));
		UploadFolderManager::setUploadFolder($this->owner, $field);
		$fields->addFieldToTab('Root.Main', $field = new DropdownField(
				'CategoryID', 'Category', DataObject::get('DocumentsPageDecorator_Category')->map()
		));
		return $fields;
	}

}

?>