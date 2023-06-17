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
      let operations;
      let expenses;
      $(function(){
        expenses = $('#expenses');
      });
      async function getExpenses() {
        let userId = "<?= $selectedUser ?>";
        let tricountId = $('#tricountId option:selected').val();
        console.log(userId);
        console.log(tricountId);
        try {
          operations = await $.getJSON("exam1s2c/get_expenses_json/"+tricountId +"/"+userId);
          console.log(operations);
          displayExpenses();
        } catch (e) {
            expenses.html("No operations!")
        }
      }

      async function inflation(){
        let amount;
        for(let i=0; i<operations.length; i++){
          let checkbox = $('#checkbox'+i)[0];
          let o = operations[i];
          if(checkbox.checked){
            amount = o.amount *= 1.1;
            try {
                await $.post();
            } catch (error) {

            }
          }
        }
        console.log(amount);
        displayExpenses();

      }
      function displayExpenses() {
        let html="<h5>Expenses initiated by this user : </h5>";
        for(let i=0; i<operations.length; i++){
          let o = operations[i];
          html +="<ul><li><input type='checkbox' id='checkbox"+i+"'>" + o.title + ", " + o.amount + "</li></ul>";
        }
        html +="<button type='button' onclick='inflation()'>Inflation</button></div>";
        expenses.html(html);
      }

    </script>
  </head>
  <body>
  <div class="main">
    <div>
        <h5>Select a user : </h5>
        <form action="exam1s2c/index" method="POST">
            <select class="selectSub" name="userId" id="userId">
              <option value="0">--Select user--</option>
                <?php foreach ($users as $u): ?>
                  <option id="subValue" value='<?= $u->getUserId() ?>'><?= $u->getFullName() ?></option>
                <?php endforeach; ?>
            </select>
            <input type="submit" value="Search tricounts">
        </form>
    </div>
    <div>
        <h5>Select a tricount : </h5>
        <select id="tricountId" onchange="getExpenses()">
            <option value="0">--Select tricount--</option>
              <?php foreach ($tricounts as $t): ?>
                <option id="subValue" value='<?= $t->get_id() ?>'><?= $t->get_title() ?></option>
              <?php endforeach; ?>
        </select>
    </div>
    <div id="expenses">


    </div>
<div>

  </body>
</html>
