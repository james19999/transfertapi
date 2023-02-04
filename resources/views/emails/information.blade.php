<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Document</title>
</head>
<body>
           <div class="container d-flex justify-content-center mt-3" >

                    <div class="row">
                          <div class="col-lg-12">
                                   <div class="card">
                                       <div class="card-header">
                                     <h4>Vos informations de connexion chez : {{ $name }}</h4>
                                    </div>
                                              <div class="card-body">

                                                <p style="font-weight: bold;"> Email : {{ $mail }}</p>
                                                <p style="font-weight: bold;" >Identifiant :  {{ $identified }}</p>
                                              </div>

                                       <div class="card-footer">

                                       </div>
                                   </div>
                          </div>
                    </div>
           </div>

</body>
</html>
