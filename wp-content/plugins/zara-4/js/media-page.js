jQuery(document).ready(function($) {
  if (adminpage === "upload-php") {

    var debugging = false;



    $(".zara-4.optimise").click(function() {
      ajaxOptimiseFromId($(this).data("id"));
    });




    $(".zara-4.restore-original").click(function() {
      ajaxRestoreOriginalFromId($(this).data("id"));
    });


    $(".zara-4.delete-original").click(function() {
      ajaxDeleteOriginalFromId($(this).data("id"));
    });



    /**
     * Format the given number of bytes.
     *
     * @param bytes
     * @returns {string}
     */
    function formatBytes( bytes ) {
      var units = [ 'B', 'KB', 'MB', 'GB', 'TB' ];

      bytes = Math.max( bytes, 0 );
      var pow = Math.floor( ( bytes ? Math.log( bytes ) : 0 ) / Math.log( 1024 ) );
      pow = Math.min( pow, units.length - 1 );

      bytes /= Math.pow( 1024, pow );

      return Math.round( bytes * 10 ) / 10 + ' ' + units[pow];
    }




    function setDisplayForIdAsProcessing(imageId) {
      var wrapper = $("#zara4-optimise-wrapper-" + imageId);

      if(wrapper.length == 0) {
        return;
      }

      var optimiseWrapper = wrapper.find(".optimise-wrapper");
      var restoreOriginalWrapper = wrapper.find(".restore-original-wrapper");
      var loadingWrapper = wrapper.find(".loading-wrapper");

      restoreOriginalWrapper.slideUp(200);
      optimiseWrapper.slideUp(200);
      loadingWrapper.slideDown(200);
    }


    function setDisplayForIdAsShowOptimise(imageId) {
      var wrapper = $("#zara4-optimise-wrapper-" + imageId);

      if(wrapper.length == 0) {
        return;
      }

      var optimiseWrapper = wrapper.find(".optimise-wrapper");
      var restoreOriginalWrapper = wrapper.find(".restore-original-wrapper");
      var loadingWrapper = wrapper.find(".loading-wrapper");

      loadingWrapper.slideUp(200);
      restoreOriginalWrapper.slideUp(200);
      optimiseWrapper.slideDown(200);
    }


    function setDisplayForIdAsShowRestoreOriginal(imageId) {
      var wrapper = $("#zara4-optimise-wrapper-" + imageId);

      if(wrapper.length == 0) {
        return;
      }

      var optimiseWrapper = wrapper.find(".optimise-wrapper");
      var restoreOriginalWrapper = wrapper.find(".restore-original-wrapper");
      var loadingWrapper = wrapper.find(".loading-wrapper");

      optimiseWrapper.slideUp(200);
      loadingWrapper.slideUp(200);
      restoreOriginalWrapper.slideDown(200);
    }



    function setRestoreOriginalData(imageId, bytesCompressed, percentageSaving) {
      var wrapper = $("#zara4-optimise-wrapper-" + imageId);

      if(wrapper.length == 0) {
        return;
      }

      var restoreOriginalWrapper = wrapper.find(".restore-original-wrapper");
      restoreOriginalWrapper.find(".compressed-size").html(bytesCompressed);
      restoreOriginalWrapper.find(".percentage-saving").html(percentageSaving);
    }


    function hideOriginalImageGroup(imageId) {
      var wrapper = $("#zara4-optimise-wrapper-" + imageId);

      if(wrapper.length == 0) {
        return;
      }

      var originalImageGroup = wrapper.find(".original-image-group");
      originalImageGroup.slideUp(200);
    }

    function showOriginalImageGroup(imageId) {
      var wrapper = $("#zara4-optimise-wrapper-" + imageId);

      if(wrapper.length == 0) {
        return;
      }

      var originalImageGroup = wrapper.find(".original-image-group");
      originalImageGroup.slideDown(200);
    }



    /**
     *
     *
     * @param imageId
     * @param successCallback
     * @param failureCallback
     */
    function ajaxOptimiseFromId(imageId, successCallback, failureCallback) {

      successCallback = successCallback != null ? successCallback : function() {};
      failureCallback = failureCallback != null ? failureCallback : function() {};

      setDisplayForIdAsProcessing(imageId);

      $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
          action: "zara4_optimise",
          id: imageId
        },
        success: function( data ) {

          if(debugging) {
            console.log(data);
          }

          if(data["error"]) {
            failureCallback(data["error"]);
            if(data["error"] == "quota_limit") {
              showOutOfQuotaModal();
            }
            setDisplayForIdAsShowOptimise(imageId);
            return;
          }

          var compression = data["compression"];

          var bytesCompressed = compression["bytes-compressed"];
          var formattedBytesCompressed = formatBytes(bytesCompressed);
          var percentageSaving = Math.round(compression["percentage-saving"] * 10) / 10;

          var alreadyOptimised = data["status"] == "already-optimised";

          setRestoreOriginalData(imageId, formattedBytesCompressed, percentageSaving);
          setDisplayForIdAsShowRestoreOriginal(imageId);

          successCallback(imageId, bytesCompressed, formattedBytesCompressed, percentageSaving, alreadyOptimised);
        },
        error: function( response ) {
          setDisplayForIdAsShowOptimise(imageId);
          failureCallback(imageId);
        }
      });

    }


    /**
     *
     *
     * @param imageId
     * @param successCallback
     * @param failureCallback
     */
    function ajaxRestoreOriginalFromId(imageId, successCallback, failureCallback) {

      successCallback = successCallback != null ? successCallback : function() {};
      failureCallback = failureCallback != null ? failureCallback : function() {};

      setDisplayForIdAsProcessing(imageId);

      $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
          action: "zara4_restore_original",
          id: imageId
        },
        success: function( response ) {
          setDisplayForIdAsShowOptimise(imageId);
          successCallback(imageId);
        },
        error: function( response ) {
          failureCallback(imageId);
        }
      });

    }



    /**
     *
     *
     * @param imageId
     * @param successCallback
     * @param failureCallback
     */
    function ajaxDeleteOriginalFromId(imageId, successCallback, failureCallback) {

      successCallback = successCallback != null ? successCallback : function() {};
      failureCallback = failureCallback != null ? failureCallback : function() {};

      setDisplayForIdAsProcessing(imageId);

      $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
          action: "zara4_delete_original",
          id: imageId
        },
        success: function( response ) {
          hideOriginalImageGroup(imageId);
          setDisplayForIdAsShowRestoreOriginal(imageId);
          successCallback(imageId);
        },
        error: function( response ) {
          failureCallback(imageId);
        }
      });

    }






    /**
     * Add bulk action options.
     */

    // Add top bulk action option
    jQuery('<option>').val('zara4_bulk_compress').text('Compress with Zara 4').appendTo('select[name="action"]');

    // Add bottom bulk action option
    jQuery('<option>').val('zara4_bulk_compress').text('Compress with Zara 4').appendTo('select[name="action2"]');


    $("body").append(
      '<div id="zara-4-bulk-modal" class="zara-4" style="display: none">' +
        '<h2>Zara 4 Bulk Compress</h2>' +

        '<div class="alert alert-boxed alert-info">' +
          '<p>The <b class="number-of-images"></b> images below will be optimised and compressed by Zara 4.</p>' +
        '</div>' +

        '<table style="width:100%; margin-top:25px" id="zara4-optimise-modal-table">' +
          '<thead>' +
            '<tr>' +
              '<th style="width:40%">Image Name</th>' +
              '<th style="width:20%">Original Size</th>' +
              '<th style="width:40%">Zara 4 Size</th>' +
            '</tr>' +
          '</thead>' +
          '<tbody id="zara4-optimise-modal-table-body"></tbody>' +
        '</table>' +

        '<hr/>' +

        '<div class="text-center">' +
          '<a href="#" id="zara4-optimise-modal-btn-optimise" class="button button-primary" style="margin-right: 5px">Compress all</a>' +
          '<a href="#" id="zara4-optimise-modal-btn-close" class="button button-default" rel="modal:close">Close</a>' +
        '</div>' +

      '</div>'
    );


    $("body").append(
      '<div id="zara-4-bulk-modal-no-images" class="zara-4" style="display: none">' +
        '<h2>Zara 4 Bulk Compress</h2>' +

        '<div class="alert alert-boxed alert-warning">' +
          '<p><b>No images to optimise</b> - No images selected</p>' +
        '</div>' +

        '<hr/>' +

        '<div class="text-center">' +
          '<a href="#" class="button button-default" rel="modal:close">Close</a>' +
        '</div>' +

      '</div>'
    );


    $("body").append(
      '<div id="zara-4-compress-all-modal" class="zara-4" style="display: none">' +
        '<h2>Zara 4 - Compress All Images</h2>' +

        '<hr/>' +

        '<div class="text-center">' +
          '<h3><span class="current-image-number"></span> of <span class="number-of-images"></span>&nbsp;&nbsp;&nbsp;&nbsp;(<span class="percentage-complete"></span>%)</h3>' +
        '</div>' +

        '<div class="progress progress-striped">' +
          '<div class="progress-bar" style="width: 0"></div>' +
        '</div>' +

        '<div class="text-center">' +
          '<div class="status-ready">' +
            'Ready to start' +
          '</div>' +
          '<div class="status-done hidden">' +
            'Finished' +
          '</div>' +
          '<div class="status-working hidden">' +
            '<img src="/wp-content/plugins/zara-4/img/loading.gif"/> Compressing, please wait' +
          '</div>' +
        '</div>' +

        '<hr/>' +

        '<div class="text-center" style="margin-top: 15px">' +
          '<div class="start-compression-btn-wrapper">' +
            '<a href="#" class="start-compression-btn button button-primary">Start Compression</a> ' +
            '<a href="#" class="button button-default" rel="modal:close">Close</a>' +
          '</div>' +
          '<div class="hidden stop-compression-btn-wrapper">' +
            '<a href="#" class="stop-compression-btn button">Stop</a>' +
          '</div>' +
          '<div class="hidden done-compression-btn-wrapper">' +
            '<a href="#" class="button button-primary" rel="modal:close">Done</a>' +
          '</div>' +
        '</div>' +

      '</div>'
    );



    $("body").append(
      '<div id="zara-4-quota-error-modal" class="zara-4" style="display: none; width: 400px">' +
        '<h2>Zara 4 - out of quota</h2>' +

        '<hr/>' +

        '<div class="alert alert-boxed alert-warning">' +
          '<p>You\'ve run out of quota - <a href="https://zara4.com/account">upgrade</a> to continue</p>' +
        '</div>' +

        '<div class="text-center mt-15">' +
          '<a href="https://zara4.com/account" class="button button-primary">Upgrade</a>&nbsp;&nbsp;' +
          '<a href="#" class="button" rel="modal:close">Close</a>' +
        '</div>' +
      '</div>'
    );



    var imageIds;
    var currentImageIdIndex;


    function showCompressAllStartWrapper() {
      $("#zara-4-compress-all-modal .start-compression-btn-wrapper").slideDown(200);
    }

    function hideCompressAllStartWrapper() {
      $("#zara-4-compress-all-modal .start-compression-btn-wrapper").slideUp(200);
    }


    function showCompressAllStopWrapper() {
      $("#zara-4-compress-all-modal .stop-compression-btn-wrapper").slideDown(200);
    }

    function hideCompressAllStopWrapper() {
      $("#zara-4-compress-all-modal .stop-compression-btn-wrapper").slideUp(200);
    }


    function showCompressAllDoneWrapper() {
      $("#zara-4-compress-all-modal .done-compression-btn-wrapper").slideDown(200);
    }

    function hideCompressAllDoneWrapper() {
      $("#zara-4-compress-all-modal .done-compression-btn-wrapper").slideUp(200);
    }


    function setCompressAllStatusWorking() {
      $("#zara-4-compress-all-modal .status-ready").slideUp(200);
      $("#zara-4-compress-all-modal .status-done").slideUp(200);
      $("#zara-4-compress-all-modal .status-working").slideDown(200);
    }

    function setCompressAllStatusReady() {
      $("#zara-4-compress-all-modal .status-working").slideUp(200);
      $("#zara-4-compress-all-modal .status-done").slideUp(200);
      $("#zara-4-compress-all-modal .status-ready").slideDown(200);
    }

    function setCompressAllStatusFinished() {
      $("#zara-4-compress-all-modal .status-working").slideUp(200);
      $("#zara-4-compress-all-modal .status-ready").slideUp(200);
      $("#zara-4-compress-all-modal .status-done").slideDown(200);
    }





    function setNumberOfImages(numberOfImages) {
      $('#zara-4-compress-all-modal .number-of-images').html(numberOfImages);
    }

    function setImageCurrentImageNumber(currentImageNumber) {
      $('#zara-4-compress-all-modal .current-image-number').html(currentImageNumber);
    }



    function setCompressAllImagesProgress(progress) {
      var percentage = progress * 100;
      $('#zara-4-compress-all-modal .progress-bar').css({ 'width': percentage + '%' });
      $('#zara-4-compress-all-modal .percentage-complete').html(percentage.toFixed(1));
    }

    function setCompressAllImagesProgressBarAsDefault() {
      $('#zara-4-compress-all-modal .progress-bar').removeClass('progress-bar-success').removeClass('progress-bar-danger').removeClass('progress-bar-warning');
    }

    function setCompressAllImagesProgressBarAsSuccess() {
      $('#zara-4-compress-all-modal .progress-bar').addClass('progress-bar-success').removeClass('progress-bar-danger').removeClass('progress-bar-warning');
    }

    function setCompressAllImagesProgressBarAsWarning() {
      $('#zara-4-compress-all-modal .progress-bar').addClass('progress-bar-warning').removeClass('progress-bar-success').removeClass('progress-bar-danger');
    }

    function setCompressAllImagesProgressBarAsDanger() {
      $('#zara-4-compress-all-modal .progress-bar').addClass('progress-bar-danger').removeClass('progress-bar-success').removeClass('progress-bar-warning');
    }





    function compressAllImagesNextImages() {

      setImageCurrentImageNumber(currentImageIdIndex);

      if(currentImageIdIndex == imageIds.length) {
        setCompressAllImagesProgress(1);
        setCompressAllImagesProgressBarAsSuccess();

        hideCompressAllStartWrapper();
        hideCompressAllStopWrapper();
        showCompressAllDoneWrapper();
        setCompressAllStatusFinished();
        return;
      }

      if(stop) {
        return;
      }

      var percentage = (currentImageIdIndex + 1) / imageIds.length;
      var id = imageIds[currentImageIdIndex];

      ajaxOptimiseFromId(id, function() {
        currentImageIdIndex++;
        setCompressAllImagesProgress(percentage);
        compressAllImagesNextImages();
      }, function(errorType) {
        if(errorType == "quota_limit") {
          setCompressAllImagesProgressBarAsDanger();
          return;
        }
        currentImageIdIndex++;
        setCompressAllImagesProgressBarAsWarning();
        setCompressAllImagesProgress(percentage);
        compressAllImagesNextImages();
      });

    }




    var stop = false;

    $('#zara-4-compress-all-modal .start-compression-btn').click(function() {
      stop = false;
      hideCompressAllStartWrapper();
      showCompressAllStopWrapper();
      setCompressAllStatusWorking();
      compressAllImagesNextImages();
    });


    $('#zara-4-compress-all-modal .stop-compression-btn').click(function() {
      stop = true;
      hideCompressAllStopWrapper();
      showCompressAllStartWrapper();
      setCompressAllStatusReady();
    });



    $("#zara-4-compress-all-modal").on("modal:after-close", function () {
      refreshTableTopAlert();
    });





    function refreshTableTopAlert() {
      $.ajax({
        type: 'POST',
        url: ajaxurl,
        data: {
          action: "zara4_uncompressed_images"
        },
        success: function( response ) {

          var numberOfImages = eval(response).length;


          if(numberOfImages == 0) {
            if($("#top-table-alert").length) {
              $("#top-table-alert").remove();
            }
            return;
          }


          if(!$("#top-table-alert").length) {

            var marginTop = true;
            var target = $(".tablenav.top").length ? $(".tablenav.top") : null;
            if(!target) {
              target = $("ul.attachments").length ? $("ul.attachments") : null;
              marginTop = false;
            }

            target.before(
              "<div id='top-table-alert' class='zara-4 alert alert-boxed alert-warning" + (marginTop ? " mt-15" : "") + "'>" +
                "<p class='small-margin'><b>Zara 4:</b>&nbsp;&nbsp;You have <b id='table-top-number-of-images'></b> images that can be compressed. Click <a href='#' id='table-top-btn'>here</a> to compress them all.</p>" +
              "</div>"
            );

            $("#table-top-btn").click(function() {

              setCompressAllImagesProgress(0);
              currentImageIdIndex = 0;

              $.ajax({
                type: 'POST',
                url: ajaxurl,
                data: {
                  action: "zara4_uncompressed_images"
                },
                success: function( response ) {

                  imageIds = eval(response);
                  var numberOfImages = imageIds.length;

                  setImageCurrentImageNumber(0);
                  setNumberOfImages(numberOfImages);

                  setCompressAllImagesProgressBarAsDefault();

                  hideCompressAllDoneWrapper();
                  hideCompressAllStopWrapper();
                  showCompressAllStartWrapper();

                  showCompressAllModal();

                  //successCallback(imageId, bytesCompressed, formattedBytesCompressed, percentageSaving, alreadyOptimised);
                },
                error: function( response ) {
                  //failureCallback(imageId);
                }
              });

            });
          }



          $("#table-top-number-of-images").html(numberOfImages);

        },
        error: function( response ) {

        }
      });
    }

    refreshTableTopAlert();
    setInterval(function() {
      refreshTableTopAlert();
    }, 10000);







    /**
     * Handles Bulk Submission
     */
    $("#posts-filter").submit(function(e) {

      var action = null;
      var imageIds = [];
      var formData = $(this).serializeArray();

      formData.forEach(function(obj) {
        if(obj.name == "media[]") {
          imageIds.push(obj.value);
        }
        if((obj.name == "action" || obj.name == "action2") && obj.value != -1) {
          action = obj.value;
        }
      });


      if(debugging) {
        console.log(action);
        console.log(imageIds);
      }

      // Only Handle Zara 4 Bulk Compress
      if(action != "zara4_bulk_compress") {
        return;
      }

      // Prevent Form Submission
      e.preventDefault();

      // --- --- --- ---

      if(imageIds.length == 0) {
        showBulkModalNoImages();
        return;
      }

      //
      // Set up modal display.
      //

      $("#zara4-optimise-modal-table-body").children().remove();
      $("#zara4-optimise-modal-table").data("image-ids", imageIds);
      $("#zara-4-bulk-modal .number-of-images").html(imageIds.length);


      // Add each image to the modal table.
      imageIds.forEach(function(imageId) {
        var optimiseBtn = $("#zara4-optimise-btn-" + imageId);
        var originalSize = $("#zara4-original-size-" + imageId).html();

        var tableRow = optimiseBtn.closest("tr");
        var filename = tableRow.find(".filename").html();

        $("#zara4-optimise-modal-table-body").append(
          "<tr id='zara4-modal-table-" + imageId + "'>" +
            "<td class='filename'>" + filename + "</td>" +
            "<td class='original-size'>" + originalSize + "</td>" +
            "<td class='zara4-size'>" +

              "<div class='queued-wrapper'>Waiting to start...</div>" +

              "<div class='already-optimised-wrapper hidden'>Skipped - Already optimised</div>" +

              "<div class='loading-wrapper hidden'>" +
                "<img src='" + LOADING_URL + "'/> Please wait" +
              "</div>" +

              "<div class='completed-wrapper hidden'>" +
                "<span class='compressed-size'></span> - <span>Saved <span class='percentage-saving'></span>%</span>" +
              "</div>" +

            "</td>" +
          "</tr>"
        );
      });

      showBulkModal();
    });


    $("#zara4-optimise-modal-btn-optimise").click(function(e) {
      e.preventDefault();
      var imageIds = $("#zara4-optimise-modal-table").data("image-ids");

      var numberOfImages = imageIds.length;
      var numberOfImagesStarted = 0;
      var numberOfImagesCompleted = 0;
      var semaphore = 0;
      var asynchronousLimit = 2;

      lockBulkModal();


      var dispatchFunction = function(imageId) {
        numberOfImagesStarted++;

        var hasNextImage = numberOfImagesStarted <= numberOfImages;
        var dispatchIsNotLocked = semaphore <= asynchronousLimit;

        if(hasNextImage && dispatchIsNotLocked) {

          // Increment Lock
          semaphore++;

          var completedCallback = function() {
            numberOfImagesCompleted++;
            semaphore--;

            if(numberOfImagesCompleted == numberOfImages) {
              completionFunction();
              return;
            }

            if(numberOfImagesStarted < numberOfImages) {
              dispatchFunction(imageIds[numberOfImagesStarted]);
            }
          };

          hideBulkModalQueued(imageId);
          hideBulkModalAlreadyOptimised(imageId);
          hideBulkModalCompleted(imageId);
          showBulkModalLoading(imageId);

          ajaxOptimiseFromId(
            imageId,

            // Success
            function(id, bytesCompressed, formattedBytesCompressed, percentageSaving, alreadyOptimised) {
              setBulkModalZara4Size(id, formattedBytesCompressed);
              setBulkModalPercentageSaving(id, percentageSaving);

              hideBulkModalLoading(id);

              if(alreadyOptimised) {
                showBulkModalAlreadyOptimised(id);
              } else {
                showBulkModalCompleted(id);
              }

              completedCallback();
            },

            // Failure
            function(id) {
              hideBulkModalLoading(id);
              showBulkModalCompleted(id);

              completedCallback();
            }
          );

        }
      };
      var completionFunction = function() {
        unlockBulkModal();
      };



      var initialDispatchCount = Math.min(asynchronousLimit, numberOfImages);
      for(var i = 0; i < initialDispatchCount; i++) {
        dispatchFunction(imageIds[i]);
      }

    });




    /**
     * Show the bulk modal.
     */
    function showBulkModal() {
      $("#zara-4-bulk-modal").modal({
        escapeClose: false,
        clickClose: false,
        showClose: false
      });
    }


    function showBulkModalNoImages() {
      $("#zara-4-bulk-modal-no-images").modal();
    }


    /**
     * Show the bulk modal.
     */
    function showCompressAllModal() {
      $("#zara-4-compress-all-modal").modal({
        escapeClose: false,
        clickClose: false,
        showClose: false
      });
    }


    /**
     * Lock the bulk modal to prevent close or submission.
     */
    function lockBulkModal() {
      $("#zara4-optimise-modal-btn-optimise").attr("disabled", "disabled");
      $("#zara4-optimise-modal-btn-close").attr("disabled", "disabled");
    }


    /**
     * Lock the bulk modal.
     */
    function unlockBulkModal() {
      $("#zara4-optimise-modal-btn-optimise").removeAttr("disabled");
      $("#zara4-optimise-modal-btn-close").removeAttr("disabled");
    }


    function setBulkModalZara4Size(imageId, zara4Size) {
      $("#zara4-modal-table-" + imageId + " .compressed-size").html(zara4Size);
    }


    function setBulkModalPercentageSaving(imageId, percentageSaving) {
      $("#zara4-modal-table-" + imageId + " .percentage-saving").html(percentageSaving);
    }





    function showBulkModalQueued(imageId) {
      $("#zara4-modal-table-" + imageId + " .queued-wrapper").slideDown(200);
    }

    function hideBulkModalQueued(imageId) {
      $("#zara4-modal-table-" + imageId + " .queued-wrapper").slideUp(200);
    }



    function showBulkModalLoading(imageId) {
      $("#zara4-modal-table-" + imageId + " .loading-wrapper").slideDown(200);
    }

    function hideBulkModalLoading(imageId) {
      $("#zara4-modal-table-" + imageId + " .loading-wrapper").slideUp(200);
    }



    function showBulkModalAlreadyOptimised(imageId) {
      $("#zara4-modal-table-" + imageId + " .already-optimised-wrapper").slideDown(200);
    }

    function hideBulkModalAlreadyOptimised(imageId) {
      $("#zara4-modal-table-" + imageId + " .already-optimised-wrapper").slideUp(200);
    }



    function showBulkModalCompleted(imageId) {
      $("#zara4-modal-table-" + imageId + " .completed-wrapper").slideDown(200);
    }

    function hideBulkModalCompleted(imageId) {
      $("#zara4-modal-table-" + imageId + " .completed-wrapper").slideUp(200);
    }




    function showOutOfQuotaModal() {
      $("#zara-4-quota-error-modal").modal({
        //closeExisting: false,
        escapeClose: false,
        clickClose: false,
        showClose: false
      });
    }


  }
});