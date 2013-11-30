<?php

#Get lists that users created. Returns default list for display.
function getLists($user) {
  try { 
    require 'database.php';
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
        echo "<li class=\"active\"><a href=\"#" . $row['listid'] . "\">". $row['name'] . "</a></li>";
        $count++;
        $firstList = $row['listid'];
      }
      else {
        echo "<li><a href=\"#" . $row['listid'] . "\">". $row['name'] . "</a></li>";
      }
  }

  return $firstList;
}

#Get lists from listid
function getListContent($listid) {
  try {
    require 'config.php';
    $db = new KissDatabase($config);
  }
  catch (Exception $e) {
    echo "Error: " . $e->getMessage();
  }

  echo "<ul>";
  foreach ($db->getListName($listid) as $row) {
    echo "<h4>" . $row['name'] . "</h4>";
  }

  foreach($db->getItemsFromList($listid) as $row) {
    echo "<li>" . $row['item'] . "</li>";
  }
  echo "</ul>";
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