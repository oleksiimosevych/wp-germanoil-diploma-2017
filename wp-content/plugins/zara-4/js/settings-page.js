jQuery(document).ready(function($) {

  var debugging = false;

  function quotaMeteringMethod(data) {
    return data['quota-metering-method'];
  }

  function quotaMeteringMethodIsDataUsage(data) {
    return quotaMeteringMethod(data) == 'data-usage';
  }

  function currentMonthlyPlanIsFree(data) {
    return data['monthly-plan']['current']['plan']['free']
  }

  function totalAllowanceRemaining(data) {
    return data['allowances']['total-remaining'];
  }

  function usedAllQuota(data) {
    return totalAllowanceRemaining(data) == 0;
  }

  function quotaIsRunningLow(data) {
    if(currentMonthlyPlanIsFree(data)) {
      return totalAllowanceRemaining(data) < (1024 * 1024 * 5);
    } else {
      return totalAllowanceRemaining(data) < (1024 * 1024 * 50);
    }
  }

  function prettyBytes(bytes) {
    var units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
    var divisions = 0;
    while(bytes >= 1024) {
      bytes /= 1024;
      divisions++;
    }
    return parseFloat(bytes).toFixed(2) + ' ' + units[divisions];
  }


  if (typeof ZARA4_API_ACCESSTOKEN == 'undefined' || ZARA4_API_ACCESSTOKEN == "" || ZARA4_API_ACCESSTOKEN == null) {
    displayAccountStatusError("Register with Zara 4 to obtain your API credentials. <a target='_blank' href='https://zara4.com/auth/api-register'>Click here</a>");
  } else {
    jQuery.ajax(
      ZARA4_API_BASE_URL + "/v1/user",
      {
        crossDomain: true,
        success: function(data, textStatus, jqXHR) {

          if(debugging) {
            console.log(data);
          }

          var plan = data['plan'];
          var monthlyFee = data['monthly-fee'];
          var monthlyQuota = data['monthly-quota'];
          var usage = data['usage'];
          var remaining = data['remaining'];

          var team = data['team'];
          var isTeamLeader = data['is-team-leader'];


          if(usedAllQuota(data)) {
            setError("<b>Your quota has run out</b> - <a target='_blank' href='https://zara4.com/account'>upgrade</a> to continue");
          } else if(quotaIsRunningLow(data)) {
            setWarning("<b>Your quota is getting low</b> - consider <a target='_blank' href='https://zara4.com/account'>upgrading</a>");
          }

          if(quotaMeteringMethodIsDataUsage(data)) {
            $("#allowance-remaining").html(prettyBytes(totalAllowanceRemaining(data)));
          } else {
            $("#allowance-remaining").html(totalAllowanceRemaining(data));
          }


          displayAccountStatusOk();

          $("#account-usage-wrapper").slideDown(200);

        },
        error: function() {
          displayAccountStatusError("Invalid API Credentials");
        },
        data: {
          "access_token": ZARA4_API_ACCESSTOKEN
        }
      }
    );
  }


  function setWarning(warningMessage) {
    $("#warning-message").html(warningMessage).css({"display":"inline-block"});
  }

  function setError(errorMessage) {
    $("#error-message").html(errorMessage).css({"display":"inline-block"});
  }



  function displayAccountStatusOk() {
    $("#zara-4-account-status").html("<img src='images/yes.png'/> Looking good");
  }


  function displayAccountStatusError(message) {
    $("#zara-4-account-status").html("<img src='images/no.png'/> " + message);
  }


  /**
   *
   *
   * @param successCallback
   * @param failureCallback
   */
  function ajaxDeleteAllOriginal(successCallback, failureCallback) {

    successCallback = successCallback != null ? successCallback : function() {};
    failureCallback = failureCallback != null ? failureCallback : function() {};


    $.ajax({
      type: 'POST',
      url: ajaxurl,
      data: {
        action: "zara4_delete_all_original"
      },
      success: function( response ) {
        successCallback();
      },
      error: function( response ) {
        failureCallback();
      }
    });

  }






  $("body").append(
    '<div id="zara-4_delete-all-backed-up-images_modal" class="zara-4" style="display: none; width: 640px">' +
      '<h2>Zara 4 - Delete backed up images</h2>' +

      '<hr/>' +

      '<div class="alert alert-boxed alert-danger">' +
        '<p>All backed up images will be deleted leaving only the compressed version.</p>' +
        '<p>This will free up storage space on your server but you will not be able to restore images.</p>' +
      '</div>' +

      '<div class="text-center"><p class="zara-4">Are you sure you want to continue?</p></div>' +

      '<div class="text-center mt-15">' +
        '<span id="zara-4_delete-all-backed-up-images_modal_delete-btn" class="button button-primary">Delete all</span>&nbsp;&nbsp;' +
        '<a id="zara-4_delete-all-backed-up-images_modal_close-btn" class="button" rel="modal:close">Cancel</a>' +
      '</div>' +
    '</div>'
  );



  $("#delete-all-btn").click(function() {
    $("#zara-4_delete-all-backed-up-images_modal").modal({
      fadeDuration: 200,
      escapeClose: false,
      clickClose: false,
      showClose: false
    });
  });


  $("#zara-4_delete-all-backed-up-images_modal_delete-btn").click(function() {
    ajaxDeleteAllOriginal(function() {
      refreshHasBackedUpImagesThatCanBeDeleted();
    }, function() {
      // Failure
    });
    $("#zara-4_delete-all-backed-up-images_modal_close-btn").click();
  });





  function refreshHasBackedUpImagesThatCanBeDeleted() {
    $.ajax({
      type: 'POST',
      url: ajaxurl,
      data: {
        action: "zara4_backed_up_images"
      },
      success: function( response ) {
        var numberOfBackUpImages = eval(response).length;
        if(numberOfBackUpImages > 0) {
          $("#delete-all-wrapper .number-of-images").html(numberOfBackUpImages);
          $("#delete-all-wrapper").slideDown();
        } else {
          $("#delete-all-wrapper").slideUp();
        }
      },
      error: function( response ) {

      }
    });
  }



  refreshHasBackedUpImagesThatCanBeDeleted();
  setTimeout(function() {
    refreshHasBackedUpImagesThatCanBeDeleted();
  }, 20000);



  $("#debug-info-btn").click(function() {
    $("#zara-4-info-modal").modal({
      escapeClose: false,
      clickClose: false,
      showClose: false
    });
  });

});