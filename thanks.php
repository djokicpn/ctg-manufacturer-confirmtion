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
    <div class="container text-center">
      <div class="row h-100 justify-content-center align-items-center">
        <div class="box">
          <div class="text-center">
            <img
              style="margin-left: 200px"
              src="./images/ctg_logo.png"
              alt=""
            />
          </div>
          <p style="font-weight: bold;">Thanks for submitting <? echo $_GET['po']; ?>;</p>
          <p>If you would like to notify us of any further changes please reach out to one<br> of the following departments to address:</p>
            
            <p>Planning: kristin@ctg.us<br>
            Logistics: isis@ctg.us<br>
            Finance: billing@ctg.us</p> 
            
            <p>Thank you.</p>

      </div>
    </div>
   
    
  </body>
</html>
