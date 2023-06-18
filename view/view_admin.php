<!DOCTYPE html>
<html lang="en">
  <head>
  <base href="<?= $web_root ?>">
    <title></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/style.css" rel="stylesheet">
    <script src="lib/jquery-3.6.3.min.js"></script>
    <script>
      let expenses;
      let operations;
      $(function() {
        expenses = $('#expenses');
      })

      async function getExpenses(){
        let userId = "<?= $selectedUser?>";
        let tricountId = $('#tricount option:selected').val();
        try {
          operations = await $.getJSON("exam1s2c/get_operations/"+tricountId+"/"+userId);
          displayExpenses();
        } catch (error) {
          expenses.html("No operations");
        }
      }

      function displayExpenses(){
        let html = "<h5>Expenses initiated by this user : </h5>";
        for(let o of operations){
          html += "<ul><li><input type='checkbox'> "+o.title+"</li></ul>";
        }
        html +="<button type='button'>Inflation</button></div>";

        expenses.html(html);
      }

    </script>
  </head>
  <body>
  <div class="main">
    <div>
        <h5>Select a user : </h5>
        <form action="exam1s2c/admin" method="POST">
            <select name="userId">
                <option>--Select user--</option>
                <?php foreach ($users as $u): ?>
                  <option id="subValue" value='<?= $u->getUserId() ?>'><?= $u->getFullName() ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Search tricounts">
        </form>
    </div>

    <div>
        <h5>Select a tricount : </h5>
        <select id="tricount" onchange="getExpenses()">
            <option value="0">--Select tricount--</option>
            <?php foreach ($subscribedTricount as $ST): ?>
              <option value="<?= $ST->get_id() ?>"><?= $ST->get_title() ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div id="expenses">
    </div>
<div>

  </body>
</html>
