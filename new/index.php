<html>
  <head>
    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
      integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z"
      crossorigin="anonymous"
    />
    <link rel="stylesheet" href="./css/bootstrap-datepicker.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="./js/bootstrap-datepicker.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js" integrity="sha512-XKa9Hemdy1Ui3KSGgJdgMyYlUg1gM+QhL6cnlyTe2qzMCYm4nAZ1PsVerQzTTXzonUR+dmswHqgJPuwCq1MaAg==" crossorigin="anonymous"></script>
    <script>
      $(document).ready(function () {
        var getUrlParameter = function getUrlParameter(sParam) {
				var sPageURL = decodeURIComponent(window.location.search.substring(1)),
					sURLVariables = sPageURL.split('&'),
					sParameterName,
					i;

				for (i = 0; i < sURLVariables.length; i++) {
					sParameterName = sURLVariables[i].split('=');

					if (sParameterName[0] === sParam) {
						return sParameterName[1] === undefined ? true : sParameterName[1];
					}
		   		}
			};
        $('#table-changes').hide();
        var fields = [];
        $(".updateButton").on("click", function () {
          let input = $(this).parent().parent().find(".changeRequest");
          input.css("display", "block");
          input.focus();

          $(this).addClass("btn-warning");
          $(this).parent().find(".confirmButton").removeClass("btn-success");
          $(this).parent().find(".btn").removeClass("btn-novalidate");
        });

        $(".confirmButton").on("click", function () {
          let input = $(this).parent().parent().find(".changeRequest");
          input.css("display", "none");

          $(this).addClass("btn-success");
          
          if(!$(this).hasClass("validated"))
            $(this).addClass("validated")
            $(this).parent().find(".updateButton").removeClass("btn-warning");
            $(this).parent().find(".btn").removeClass("btn-novalidate");
        });

        $("#palletCount").on("keyup", function (event) {
          if ($(this).val() > 50) $(this).val(50);
        });

        $(".dateInput").datepicker({
          format: "dd-M-yyyy",
          autoclose: true,
        });

        $('#submit').on('click',function(e) {
          e.preventDefault()
          checkButtons()
          if(validate()) {
            if(fields.length > 0) {
              $('#table-changes').show();
            } else {
              sendData()
              return
            }
            $("#table-body").html('');
            for(let i = 0 ; i< fields.length ; i++) {
              let line = "<tr> <td>" + fields[i].field + "</td> <td> " + fields[i].oldValue + " </td> <td> " + fields[i].newValue + " </td> </tr>"
              $('#table-body').append(line);
            }
            $('#confirmation-modal').modal('show');
          } else {
          swal({
            title: "Validation failed!",
            text: "All fields are required!",
            icon: "warning",
            button: "Update",
          });
          }
          
          
        })

        $('#confirm-modal').click(function() {
          
          sendData()
          
        })
        function sendData() {
          $.ajax({
            dataType: 'json',
            method: 'POST',
            url: "netsuite.php?id=" + getUrlParameter('id') + "&token=" + getUrlParameter('token') + "&user=" + getUrlParameter('user'),
            data: {
                fields:fields
            },
            success: function (data) {
              console.log("Success: " + data)
              // swal({
              //     title: "Thank you for confirming our purchase order!!",
              //     text: "If any changes have been requested we will review and will be in touch with you shortly.",
              //     icon: "success",
              //     timer: 3000,
              //     buttons: false,
              //     }).then( ()=> {
              //       window.location = "thanks.html";
              //     })
       
            },
            error: function (e) {
              console.log("Error: " + JSON.stringify(e))
              // swal({
              //     title: "Thank you for confirming our purchase order!!",
              //     text: "Now go make some great products!",
              //     icon: "success",
              //     timer: 3000,
              //     buttons: false,
              //     }).then( ()=> {
              //       window.location = "thanks.html";
              //     })
            }

          }).done(function (data) {

          // location.replace('post.php')
          // swal("Thank you!", "You successfuly submitted form!", "success");
          });
        }
        function checkButtons() {
          fields = [];
          // console.log('Fields changed:')
          $('.btn-warning').each(function () {
            let field = $( this ).parent().parent().find('.my-input').attr('field');
            
            let oldValue = $( this ).parent().parent().find('.old-value').text();
            let value = $( this ).parent().parent().find('.my-input').children().val()
            let obj = {field: field, oldValue: oldValue, newValue:value}
            fields.push(obj)
          })
          if($('#anyOtherComments').val() && $('#anyOtherComments').val().length > 0) {
            let obj = {field: 'Other changes', oldValue: '', newValue: $('#anyOtherComments').val()}
            fields.push(obj)
          }
          // console.log(fields)
        }
        function validate() {
          let ready = true;
          $('.myButton').each(function() {
            if(!$(this).parent().find(".btn-warning").hasClass('btn') && !$(this).parent().find(".btn-success").hasClass('btn')) {
              $(this).addClass('btn-novalidate')
              ready = false;
            }
          })
          $('.my-input').each(function() {
            let btnWarning = $(this).parent().parent().find('.buttons').find('.updateButton').hasClass('btn-warning');
            if(btnWarning) {
              if(!$(this).children().val()) {
                  $(this).children().addClass('is-invalid')
                  ready = false;
              }
            }
          })
          return ready;
        }
        $('body').on('change','.is-invalid', function() {
          $(this).removeClass('is-invalid')
        })
      });
    </script>
  </head>
  <style>
    .btn-novalidate{ 
      border: 1px solid red !important;
    }
    .changeRequest {
      display: none;
    }
    .myButton {
      border: 1px solid gray;
    }
    .box {
      box-shadow: 0 0.46875rem 2.1875rem rgba(4, 9, 20, 0.03),
        0 0.9375rem 1.40625rem rgba(4, 9, 20, 0.03),
        0 0.25rem 0.53125rem rgba(4, 9, 20, 0.05),
        0 0.125rem 0.1875rem rgba(4, 9, 20, 0.03);
      border-width: 0;

      background-color: #ffffff !important;
      border-radius: 10px;
      padding: 10px;
    }
    .less-space {
      line-height: 30%;
    }
    .table td,
    .table th {
      padding: 0.25rem;
      vertical-align: top;
      border-top: 1px solid #dee2e6;
    }
  </style>
  <body>
    
    <div class="container">
      <div class="row h-100 justify-content-center align-items-center">
        <div class="box">
          <div class="text-center">
            <img
              style="margin-left: 200px"
              src="./images/ctg_logo.png"
              alt=""
            />
          </div>
          <p>Good Day</p>
          <p>
            Our records indicate we are awaiting your confirmation that
            <span><b>P0 2004831</b></span>
            sent <span><b id="date_approved">9-Sep-2020</b></span> has been
            received, reviewed and accepted.
          </p>
          <p>
            Take a moment to review all details paying particular attention to:
          </p>
          <br />
          <table class="table table-hover">
            <thead class="thead-dark">
              <th>Field</th>
              <th>Value</th>
              <th>Action</th>
              <th>Update Request</th>
            </thead>
            <tbody>
              <tr>
                <td>Pick up date:</td>
                <td class='old-value'>17-Sep-2020</td>
                <td class="buttons">
                  <button class="btn btn-sm myButton confirmButton">
                    Confirm
                  </button>
                  <button class="btn btn-sm myButton updateButton">
                    Update
                  </button>
                </td>
                <td>
                  <div class="my-input" field="Pick up date">
                  <input
                    placeholder="Pick Up Date"
                    class="form-control changeRequest dateInput"
                    type="text"
                  />
                  </div>
                </td>
              </tr>
              <tr>
                <td>Shipment Type:</td>
                <td class='old-value'>20 GP</td>
                <td class="buttons">
                  <button class="btn btn-sm myButton confirmButton">
                    Confirm
                  </button>
                  <button class="btn btn-sm myButton updateButton">
                    Update
                  </button>
                </td>
                <td>
                  <div
                    class="form-group changeRequest my-input" field="Shipment Type"
                    style="margin-bottom: 0rem"
                  >
                  
                    <select id="tell_us" name="tell_us" class="form-control">
                      <option value="99" selected disabled>
                        Please select...
                      </option>
                      <optgroup label="20">
                        <option value="20FT">20 GP</option>
                        <option value="20RE">20 RE</option>
                      </optgroup>
                      <optgroup label="40">
                        <option value="40FT">40 GP</option>
                        <option value="40HC">40 HC</option>
                        <option value="40RE">40 RE</option>
                      </optgroup>
                      <optgroup label="Other">
                        <option value="Airfreight">Airfreight</option>
                        <option value="LTL">LTL</option>
                        <option value="FTL">Full Truck Load</option>
                      </optgroup>
                    </select>
                  </div>
                </td>
              </tr>
              <tr>
                <td>Pallet Count:</td>
                <td class='old-value'>11</td>
                <td class="buttons">
                  <button class="btn btn-sm myButton confirmButton">
                    Confirm
                  </button>
                  <button class="btn btn-sm myButton updateButton">
                    Update
                  </button>
                </td>
                <td>
                  <div class="my-input" field="Pallet Count">
                  <input
                    placeholder="Max: 50; Partial: No"
                    class="form-control changeRequest"
                    id="palletCount"
                    type="number"
                    onkeydown="if(event.key==='.' || event.key==='-'){event.preventDefault();}"
                    onpaste="let pasteData = event.clipboardData.getData('text'); if(pasteData){pasteData.replace(/[^0-9]*/g,'');} "
                  />
                  </div>

                </td>
              </tr>
              <tr>
                <td>Produced For:</td>
                <td>Serbia</td>
                <td>
                  <button class="btn btn-sm myButton confirmButton producedFor">
                    Confirm
                  </button>
                </td>
                <td>
                </td>
              </tr>
              <tr>
                <td>Pick Up Address:</td>
                <td class='old-value'>NW Ave 115b, 33512 Miami, FL</td>
                <td class="buttons">
                  <button class="btn btn-sm myButton confirmButton">
                    Confirm
                  </button>
                  <button class="btn btn-sm myButton updateButton">
                    Update
                  </button>
                </td>
                <td>
                  <div class="my-input" field="Pick Up Address">
                  <input
                    placeholder="Full Address"
                    class="form-control changeRequest"
                    type="text"
                  />
                  </td>

                </td>
              </tr>
              <tr>
                <td>Special Comments:</td>
                <td class='old-value'>Default comment here or no comment at all</td>
                <td class="buttons">
                  <button class="btn btn-sm myButton confirmButton">
                    Confirm
                  </button>
                  <button class="btn btn-sm myButton updateButton">
                    Update
                  </button>
                </td>
                <td>
                  <div class="my-input" field ="Special Comments">
                  <input class="form-control changeRequest" type="text" placeholder="Special Comments" />
                </td>
              </div>
              </tr>
            </tbody>
          </table>

          <br />

          <form action="">
            

            <div class="form-group">
              <label for="anyOtherComments">For any other changes please describe below: </label>
              <textarea
                class="form-control"
                id="anyOtherComments"
                rows="3"
                
              ></textarea>
            </div>
            <div class="text-center">
              <button type="submit" class="btn btn-primary" id='submit'>Submit Form</button>
            </div>
          </form>
          <div id='email'></div>
        </div>
      </div>
    </div>
   
    
    <div class="modal fade bd-example-modal-lg" id="confirmation-modal" tabindex="-1" role="dialog" aria-labelledby="confirmation-modal" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="confirmation-modal">Requested changes</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
        
          <p>Summary of requested changes: </p>

          <table class = 'table table-hover' id='table-changes'>
              <thead class="thead-dark">
                  <th>Field</th>
                  <th>Old Value</th>
                  <th>New Value</th>
              </thead>
              <tbody id='table-body'>
              
              </tbody>
          </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id='confirm-modal'>Confirm</button>
          </div>
        </div>
      </div>
    </div>

  </body>
</html>
