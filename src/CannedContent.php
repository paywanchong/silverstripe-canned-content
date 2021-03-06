<?php

namespace Fractas\CannedContent;

use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\Tab;
use SilverStripe\Forms\TabSet;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\ORM\DataObject;
use SilverStripe\Security\Permission;
use SilverStripe\Core\Injector\Injector;
use SilverStripe\ORM\DB;
use SilverStripe\Dev\YamlFixture;

class CannedContent extends DataObject
{
    private static $table_name = 'CannedContent';

    private static $db = array(
        'Name' => 'Varchar(255)',
        'Description' => 'Varchar(255)',
        'Content' => 'HTMLText',
        'IsActive' => 'Boolean',
    );

    private static $defaults = array(
        'IsActive' => true,
    );

    private static $casting = array(
        'Title' => 'Varchar',
        'Link' => 'Varchar',
    );

    private static $indexes = array();

    private static $default_sort = 'ID DESC';

    private static $summary_fields = array(
        'Name',
        'IsActive.Nice',
        'Created.Nice',
    );

    private static $searchable_fields = array(
        'Name' => 'PartialMatchFilter',
    );

    private static $field_labels = array(
        'Name' => 'Name for Canned Content Template',
        'IsActive.Nice' => 'Is Active',
        'Created.Nice' => 'Created',
    );

    private static $singular_name = 'Canned Content Template';

    public function i18n_singular_name()
    {
        return _t('HtmlEditorFieldContentTemplate.CONTENTTEMPLATE', 'Canned Content Template');
    }

    private static $plural_name = 'Canned Content Template';

    public function i18n_plural_name()
    {
        return _t('HtmlEditorFieldContentTemplate.CONTENTTEMPLATES', 'Canned Content Templates');
    }

    public function populateDefaults()
    {
        parent::populateDefaults();
    }

    public function getCMSFields()
    {
        $fields = new FieldList(new TabSet('Root', new Tab('Main')));

        $fields->addFieldsToTab('Root.Main', array(
            new TextField('Name', 'Name'),
            new CheckboxField('IsActive', 'Is Active'),
            new TextareaField('Description', 'Description'),
            new HtmlEditorField('Content', 'Content'),
        ));

        return $fields;
    }

    public function Link()
    {
        return '/cannedcontent/template/'.$this->ID;
    }

    public function Title()
    {
        return $this->getTitle();
    }

    public function getTitle()
    {
        $out = '';

        return $this->Name.$out;
    }

    public function canView($member = null)
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canEdit($member = null)
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canDelete($member = null)
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function canCreate($member = null, $context = array())
    {
        return Permission::check('CMS_ACCESS_CMSMain', 'any', $member);
    }

    public function requireDefaultRecords()
    {
        parent::requireDefaultRecords();

        $anyrecord = self::get()->first();
        if (!$anyrecord) {
            $factory = Injector::inst()->create('SilverStripe\Dev\FixtureFactory');
            $fixture = YamlFixture::create('vendor/fractas/canned-content/tests/fixtures.yml');
            $fixture->writeInto($factory);

            DB::alteration_message('Default Canned Content Templates created', 'created');
        } else {
            DB::alteration_message('Skipping, Canned Content Templates already created', 'created');
        }
    }
}
