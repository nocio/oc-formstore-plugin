<?php namespace Nocio\FormStore\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class Submissions extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'nocio.formstore.view_submissions',
        'nocio.formstore.manage_submissions'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('Nocio.FormStore', 'main-menu-item', 'submissions-menu-item');
    }
    
    public function renderDataPreview() {
        $submission = $this->widget->form->model;

        if (! $model = $submission->data()->first()) {
            return 'No data available, as the submission has been cancelled by the user.';
        }
        
        $config = $this->makeConfig($submission->form->getFieldsConfig());
        $config->model = $model;
        
        $formWidget = new \Backend\Widgets\Form($this, $config);
        
        return $formWidget->render(['preview' => true]);
    }
    
    public function renderRelationsPreview() {
        $submission = $this->widget->form->model;
        
        if (! $relations = $submission->form->rels()->get()) {
            return false;
        }
        
        $html = '<hr />';
        foreach($relations as $relation) {
            if (! $rows = $submission->getDataField($relation->field)) {
                continue;
            }
            
            $html .= '<h4>' . $relation->title . '</h4>';
            
            foreach($rows->get() as $row) {
                $config = $this->makeConfig($relation->target->getFieldsConfig());
                $config->model = $row;
        
                $formWidget = new \Backend\Widgets\Form($this, $config);
        
                $html .= $formWidget->render(['preview' => true]);
            }
        }

        return $html;
    }

    public function exportMails()
    {
        $emails = [];

        $lists = $this->makeLists();

        $definition = true;
        $widget = isset($lists[$definition])
            ? $lists[$definition]
            : reset($lists);

        $model = $widget->prepareModel();
        $results = $model->get();
        foreach ($results as $result) {
            $email = $result->submitter_id;
            if (! in_array($email, $emails) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $emails[] = $email;
            }
        }

        $result = implode(',', $emails);

        header("Content-Type: text/plain");
        header('Content-Disposition: attachment; filename="emails.txt"');
        header("Content-Length: " . strlen($result));
        echo $result;
        exit;
    }
}
