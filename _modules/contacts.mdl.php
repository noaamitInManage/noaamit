<?
include_once($_SERVER['DOCUMENT_ROOT'].'/_modules/_inc/header.php');
?>

<div class="container">
    <h1>Contact Us</h1>

    <div class="card card-body bg-light mt-5">
        <h2>צור פנייה חדשה</h2>
        <form method="post" id="contactForm">
            <div class="form-group">
                <label for="full_name">full name: <sup>*</sup></label>
                <input type="text" name="full_name"
                       class="form-control form-control-lg" required>

            </div>
            <div class="form-group">
                <label for="email">email: <sup>*</sup></label>
                <input type="text" name="email"
                       class="form-control form-control-lg" required>
            </div>
            <div class="form-group">
                <label for="title">Title: <sup>*</sup></label>
                <input type="text" name="title"
                       class="form-control form-control-lg" required>
            </div>
            <div class="form-group">
                <label for="message">message: <sup>*</sup></label>
                <textarea name="message"
                          class="form-control form-control-lg" required></textarea>
            </div>
            <div id="answers"></div>
            <input type="submit" class="btn btn-success" value="Submit" id="submitForm">
        </form>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.js"></script>
    <script type="text/javascript">

        $("#submitForm").click(() => {
            $('#contactForm').validate({

                submitHandler: function(form) {
                    var formData = $('form').serialize();

                    $.ajax({
                        url:"https://noaa.inmanage.com/api/website/1.0/sendNewContact/",
                        type: 'post',
                        data: formData,
                        success: function(response) {
                            $('#answers').html("message sent");
                            document.getElementById("contactForm").reset();
                            console.log(response);
                            // $('#answers').html(response);
                        }
                    });

                }
            });

        });


    </script>
    <? include_once($_SERVER['DOCUMENT_ROOT'].'/_modules/_inc/footer.php'); ?>
