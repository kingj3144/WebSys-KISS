<?php
    require_once 'database.php';

#Get lists that users created. Returns default list for display.
function getLists($user) {
  try {
    require 'config.php';
    $db = new KissDatabase($config);
  }
  catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }

  $count = 0;
  $firstList = 0;
  foreach($db->getListsFromUser($user) as $row)
  {
    if ($count == 0) {
      $listFromDB = getListContent(false, $row['listid']);
      $accessList = getAccessList($row['listid']);
      echo "<li class=\"active\"><a id=\"" . $row['listid'] . "\" href=\"#\" 
            onclick=\"getList('" . $listFromDB . "', '" . $accessList . "');
            return false;\">". $db->getListName($row['listid']) . "</a></li>";
      $count++;
      $firstList = $row['listid'];
    }
    else {
      $listFromDB = getListContent(false, $row['listid']);
      $accessList = getAccessList($row['listid']);
      echo "<li><a id=\"" . $row['listid'] . "\" href=\"#\" 
            onclick=\"getList('" . $listFromDB . "', '" . $accessList . "');
            return false;\">". $row['name'] . "</a></li>";
    }
  }

  return $firstList;
}

#Get lists from listid
function getListContent($first, $listid) {
  try {
    require "config.php";
    $db = new KissDatabase($config);
  }
  catch (Exception $e) {
    return "Error: " . $e->getMessage();
  }

  $list = "<h4>" . $db->getListName($listid) . "</h4>";

  if ($first == true) {
    $list .= "<table class=\"table table-condensed\">";
  }
  else {
    $list .= "<table class=&quot;table table-condensed&quot;>";
  }

  foreach($db->getItemsFromList($listid) as $row) {
    $list .= "<tr><td>" . $row['item'] . "</td><td>" . 
          $row['quantity'] . " " . $row['unit'] . "</td></tr>";
  }
  $list .= "</table>";
  return $list;
}

# get access list
function getAccessList($listid) {
    try {
    require 'config.php';
    $db = new KissDatabase($config);
  }
  catch (Exception $e) {
    return "Error: " . $e->getMessage();
  }

  $list = "<h4>Access List</h4><ul>";
  foreach($db->getAccessList($listid) as $row) {
    $list .= "<li>" . $row['username'] . "</li>";
  }
  $list .= "</ul>";
  return $list;
}

function editList() {
  echo "<form class=\"form-inline\" role=\"form\" action=\"addForms.php\">
          <div class=\"form-group\">
            <label class=\"sr-only\" for=\"itemName\" class=\"input-small\"></label>
              <input type=\"text\" class=\"form-control input-normal\" id=\"itemName\" placeholder=\"Item Name\">
           </div>
          <div class=\"form-group\">
            <label class=\"sr-only\" for=\"quantity\"></label>
            <input type=\"number\" class=\"form-control input-small\" id=\"quantity\" placeholder=\"Quantity\" min=\"0\">
          </div>
          <div class=\"form-group\">
            <label class=\"sr-only\" for=\"unit\"></label>
            <input type=\"text\" class=\"form-control input-small\" id=\"unit\" placeholder=\"Unit\">
          </div>
          <button type=\"submit\" class=\"btn btn-default\">Add Item</button>
        </form>";
}

function addEditors() {
  echo "<form class=\"form-inline\" role=\"form\" action=\"addForms.php\">
          <div class=\"form-group\">
            <label class=\"sr-only\" for=\"username\" class=\"input-small\"></label>
              <input type=\"text\" class=\"form-control input-normal\" id=\"username\" placeholder=\"Username\">
           </div>
          <button type=\"submit\" class=\"btn btn-default\">Add User</button>
        </form>";
}
?>