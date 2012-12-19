<?php

require_once '../config/config.php';

class Cron
{

  static function doConnect()
  {
    mysql_connect(DB_HOST, DB_USER, DB_PASS);
    @mysql_select_db(DB_CATALOG) or die("Unable to select database");
    $query = "SET NAMES 'utf8'";
    mysql_query($query);
  }

  static function doClose()
  {
    mysql_close();
  }

  static function refreshOdeskSkills()
  {
    try {
      $odesk_skills = json_decode(file_get_contents('http://www.odesk.com/api/profiles/v1/metadata/skills.json'));
      $skills = $odesk_skills->skills;

      $query = 'INSERT INTO odesk_skills (skill, pretty_name, external_link, description, wikipedia_page_id) VALUES ' . "\n";
      $i = 0;
      $count = count($skills);
      foreach ($skills as $k => $skill) {
        sleep(5);
        $skill_data = json_decode(file_get_contents('http://www.odesk.com/api/profiles/v1/metadata/skills/' . $skill . '.json'));
        $query .= sprintf("('%s', '%s', '%s', '%s', %s)", mysql_real_escape_string($skill_data->skill->skill), mysql_real_escape_string($skill_data->skill->skill), mysql_real_escape_string($skill_data->skill->external_link), mysql_real_escape_string($skill_data->skill->description), 0
        );
        $i++;
        if ($i >= $count) {
          $query .= ";";
          break;
        } else {
          $query .= ",\n";
        }
      }
      $fp = fopen('assets/cron_p/odesk-skills.sql', 'w');
      fwrite($fp, $query);
      fclose($fp);
      header("HTTP/1.1 200 Success");
    } catch (Exception $exc) {
      header("HTTP/1.1 500 Internal Server Error");
    }
  }

}