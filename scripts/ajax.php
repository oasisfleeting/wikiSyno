<?php

$data = array(
  'synonyms' => array(),
  'total' => 0,
);
if (isset($_REQUEST['term'])) {
  $term = strip_tags(trim($_REQUEST['term']));
  $synoms = Application::getSynonyms($term);
  $data['synonyms'] = $synoms;
  $data['total'] = count($synoms);
}
echo json_encode($data);
die();