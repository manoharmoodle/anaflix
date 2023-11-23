<?php
class leaderboard_table extends table_sql {
    // function __construct($uniqueid) {
    //     parent::__construct($uniqueid);
    // }
     function __construct($uniqueid)
    {
        parent::__construct($uniqueid);
        // Define the list of columns to show.
        $columns = array('num', 'username','data', 'anacoins');
        $this->define_columns($columns);

        // Define the titles of columns to show in header.
        $headers = array('S No.', 'Employe Name', 'Business Line', 'Anacoins Points');
        $this->define_headers($headers);
    }
    
    function col_num($values) {
        
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {
            return $values->num;
        } else {
            return $values->num;
        }
    }
    
    function col_username($values) {
        
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {
            return $values->username;
        } else {
            return $values->username;
        }
    }
    
    function col_data($values) {
        
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {
            return $values->data;
        } else {
            return $values->data;
        }
    }
    
    function col_anacoins($values) {
        
        // If the data is being downloaded than we don't want to show HTML.
        if ($this->is_downloading()) {
            return $values->anacoins;
        } else {
            return $values->anacoins;
        }
    }    
}
