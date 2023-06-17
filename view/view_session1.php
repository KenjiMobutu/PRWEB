<!DOCTYPE html>
<html lang="en">

<head>
    <title>Session1</title>
    <base href="<?= $web_root ?>">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="lib/jquery-3.6.3.min.js"></script>
    <style>
        .button-container {
            display: flex;
            flex-direction: row;
            justify-content: center;
        }
    </style>
    <script>

        async function add(){
            console.log("ADD");
            const notSubscribedTricount = $('#NotSubscribedTricount option:selected');
            const notSubscribedTricountId = notSubscribedTricount.val();
            $('#subscribedTricount').append(notSubscribedTricount);
            console.log(notSubscribedTricountId );
            const id = $('#userId option:selected').val();
            await $.post("participation/add_service/" + notSubscribedTricountId, { "names": id });
        }

    </script>
</head>

<body>
    <nav class="navbar  fixed-top  navbar-expand-lg" style="background-color: #e3f2fd;">
        <div class="container-fluid">
            <a class="btn btn-sm btn-outline-danger" type="button" href="user">Back</a>
            <span class="navbar-text"><b>Session 1</b></span>
        </div>
    </nav>
    <div class="pt-5 pb-3"></div>
    <div class="main pb-2">
        <form action="tricount/postSession1" method="post">
            <div class="row">
                <div class="col-9">
                    <select class="form-select" name="userId" id="userId">
                        <option>-- Select a User --</option>
                        <?php foreach ($users as $u): ?>
                          <option id="subValue" value='<?= $u->getUserId() ?>' <?= ($u->getUserId() == $userId )? 'selected' : '' ?>><?= $u->getFullName() ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-3">
                    <button class="btn btn-outline-secondary" type="submit">Show</button>
                </div>
            </div>

            <div class="form-label mt-2">Participates in these tricounts</div>
            <select class="selectSub" name="subscribedTricount" id="subscribedTricount" onchange="getOpeByTricountAndInitiator()">
              <option value="" >--All Subscribed Tricount--</option>
                <?php foreach ($subscribedTricount as $ST): ?>
                  <option id="subValue" value='<?= $ST->get_id() ?>' ><?= $ST->get_title() ?></option>
                <?php endforeach; ?>
            </select>

            <div>
                <button type="button" onclick="add()"> Up</button>
            </div>

            <div class="form-label mt-2">Does not participate in these tricounts</div>
            <select id="NotSubscribedTricount">
                <?php foreach ($notSubscribedTricount as $NST): ?>
                  <option id="notSubscribed" value='<?= $NST->get_id() ?>' ><?= $NST->get_title() ?></option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>


</body>

</html>
