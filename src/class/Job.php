<?php

/**
 * Description of Job
 *
 * @author Charles
 */
class Job {
    private $id = null;
    
    public function setId($id) {
        $this->id = $id;
    }
    
    public function getId() {
        return (!is_null($this->id)) ? $this->id : 'Not set';
    }
    
    public function getJobDetails() {
        $query = sprintf("SELECT job_grades.*
                          FROM job_grades
                          WHERE job_grades.id=%d",
                          $this->id);
        MySQL::setQuery($query);
        $data = MySQL::fetchRow();
        return $data;
    }
    
    public function getOptions() {
        $query = sprintf("SELECT job_grades.id,
                          job_grades.job_type
                          FROM job_grades
                          ORDER BY job_grades.job_type ASC");
        $options = '<option value="">--</option>';
        MySQL::setQuery($query);
        foreach (MySQL::fetchRows() as $key => $value) {
            $selected = ($this->id == $value['id']) ? ' selected="selected"' : '';
            $options .= '<option value="'. $value['id'] .'"'. $selected .'>'. ucwords($value['job_type']) .'</option>';
        }
        return $options;
    }
    
    public function isAccepted() {
        $query = sprintf("SELECT job.acceptance
                          FROM job_grades job
                          WHERE job.id=%d",
                          $this->id);
        MySQL::setQuery($query);
        $result = MySQL::fetchRow();
        return $result['acceptance'];
    }
}

?>
