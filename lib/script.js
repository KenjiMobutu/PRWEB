
/*$(document).ready(function() {

  // Calcul des montants initiaux
    recalculateAmounts();

  // Fonction pour recalculer les montants à chaque modification
    function recalculateAmounts() {
      // Obtenir le montant total de l'opération
        var totalAmount = parseFloat($("#amount").val());
        console.log(totalAmount);

      // Obtenir le poids total
        var totalWeight = 0;
        $(".checks input[type='number']").each(function() {
            totalWeight += parseFloat($(this).val());
        });
        console.log($(".checks input[type='number']"));

      // Calculer les montants à payer pour chaque utilisateur
        $(".checks input[type='checkbox']").each(function() {
            var user = $(this).val();
            var weight = parseFloat($("#" + user).val());
            var amount = (weight / totalWeight) * totalAmount;
            $("#" + user + "_amount").val(amount.toFixed(2));
        });
    }

  // Attacher des gestionnaires d'événements aux champs pertinents
    $("#amount, .checks input[type='number'], .checks input[type='checkbox']").change(function() {
        recalculateAmounts();
    });

  // Gérer les cases à cocher qui changent de poids
    $(".checks input[type='number']").change(function() {
        var weight = parseFloat($(this).val());
        var checkbox = $(this).siblings("input[type='checkbox']");
        if (weight === 0) {
            checkbox.prop("checked", false);
        } else {
            checkbox.prop("checked", true);
        }
        recalculateAmounts();
    });
    console.log($(".checks input[type='number']"));
});*/

function calculateAmounts() {
    // Get the total amount
    var totalAmount = parseFloat($("#amount").val());
    console.log("MONTANT TOTAL",totalAmount);
    // Get the weight for each user and calculate the total weight
    var weights = {};
    var totalWeight = 0;
    $("input[type='number'][id$='_weight']").each(function() {
        var userId = $(this).attr("id").replace("_weight", "");
        var weight = parseFloat($(this).val());
        weights[userId] = weight;
        totalWeight += weight;
    });
    console.log("TOTAL WEIGHTS :",totalWeight);
    console.log("WEIGHTS",$("input[type='number'][id$='_weight']"));

    // Calculate the amount owed by each user
    /*var amounts = {};
    $("input[type='checkbox'][id^='c_']").each(function() {
        var userId = $(this).attr("id");
        var isChecked = $(this).is(":checked");
        console.log(isChecked);
        var weight = weights[userId];
        console.log(weight );
        var amount = 0;
        if (isChecked && weight > 0) {
            amount = totalAmount * weight / totalWeight;
        }
        amounts[userId] = amount;
        $("#" + user + "_amount").val(amount.toFixed(2));
        console.log("Montant/user",$("#" + userId + "_amount").val(amount.toFixed(2)));
    });*/


    // Gérer les cases à cocher qui changent de poids
   /* $(".checks input[type='number']").change(function() {
        var weight = parseFloat($(this).val());
        var checkbox = $(this).siblings("input[type='checkbox']");
        if (weight === 0) {
            checkbox.prop("checked", false);
        } else {
            checkbox.prop("checked", true);
        }
        //calculateAmounts();
    });*/

    // Calculer les montants à payer pour chaque utilisateur
    $(".checks input[type='checkbox']").each(function() {
        var user = $(this).val();
        var isChecked = $(this).is(":checked");
        console.log("EST-COCHÉ :", isChecked);
        var weight = parseFloat($("#" + user).val());
        console.log("POIDS :", weight);
        var amount = 0;
        if (isChecked && weight > 0) {
            var amount = (weight / totalWeight) * totalAmount;
        }
        $("#" + user + "_amount").val(amount.toFixed(2));
    });


    // Calculate the total amount owed by each user
    $("input[type='number'][id$='_dette']").each(function() {
        var userId = $(this).attr("id").replace("_dette", "");
        var totalAmount = 0;
        $("input[type='number'][id$='_amount']").each(function() {
            var paidByUserId = $(this).attr("id").replace("_amount", "");
            var amount = parseFloat($(this).val());
            if (paidByUserId == userId) {
                totalAmount += amount;
            }
        });
        $(this).val(totalAmount);
        console.log("DETTE",$(this).val(totalAmount));
    });


}

$(document).ready(function() {
    // Calculate the amounts when the page is loaded
    calculateAmounts();

    // Add event listeners to the input fields
    $("input[type='number'], input[type='checkbox']").change(function() {
        calculateAmounts();
        console.log("Input ou CheckBox a changé");
    });
    console.log("LISTENERS",$("input[type='number'], input[type='checkbox']"));
});






