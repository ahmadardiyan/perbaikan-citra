<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	
	<title> PERBAIKAN CITRA </title>

	<link href="css/bootstrap.min.css" rel="stylesheet" >
	<link href="style.css" rel="stylesheet" >

</head>
<body>
	<div class="container">

		<center>
			<h1 class="page-header">PERBAIKAN CITRA</h1>

			<form method="post" enctype="multipart/form-data">
				
				<div class="row">
					<div class="col-md-4"> 
						<input type="file" name="image" style="padding-bottom: 30px">

						<button type="submit" class="btn btn-success" name="submit"> Upload Image </button>
					</div>

					<div class="col-md-4">
						<div class="row" style="padding-bottom: 20px">
							<input type="text" class="form-control" name="prosentase" placeholder="Masukkan Prosentase">
							<br>
							<button type="submit" class="btn btn-success" name="add"> Add </button>
						</div>

						<div class="row">
							<button type="submit" class="btn btn-primary" name="gaussian"> Gaussian </button>
							<button type="submit" class="btn btn-primary" name="saltpepper"> Salt and Pepper </button>
							<button type="submit" class="btn btn-primary" name="speckle"> Speckle </button>
						</div>
					</div>

					<div class="col-md-4" style="padding-top: 50px">
						<button type="submit" class="btn btn-success" name="reduce"> Reduce </button>
					</div>
				</div>

			</form>

			<br>

			<div class="row">
				<div class="col-md-4"> <!-- Upload Image And Image Ori -->
					<label> GAMBAR ASLI </label>
					<div class="row">
						<?php 
						error_reporting(0);

						session_start();
						
						// Upload
						if(isset($_POST["submit"])) 
						{
							$_SESSION['image_location']	= $_FILES["image"]["tmp_name"];
							$_SESSION['image_name']		= $_FILES["image"]["name"];
							$_SESSION['image_ext']		= strtolower(pathinfo($_SESSION['image_name'], PATHINFO_EXTENSION));
							$_SESSION['image_name_ori']	= "img-0.".$_SESSION['image_ext'];

							if (empty($_SESSION['image_name'])) 
							{
								?>
								<div class="alert alert-danger">
									<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
									<strong>Error!</strong> Masukkan gambar !
								</div>

								<?php
							}
							else 
							{
								if ($_SESSION['image_ext'] == "jpg" || $_SESSION['image_ext'] == "jpeg" || $_SESSION['image_ext'] == "png") 
								{
									if (copy($_SESSION['image_location'], $_SESSION['image_name_ori'])) {
										$_SESSION['image_status'] = "berhasil";
									}
									else 
									{
										?>
										<div class="alert alert-danger">
											<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
											<strong>Error!</strong> Gambar gagal diunggah
										</div>"

										<?php
									}
								}
								else 
								{
									?>
									<div class="alert alert-danger">
										<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
										<strong>Error!</strong> Masukkan extensi gambar yang bertipe JPG/JPEG/PNG
									</div>

									<?php	
								}
							}
						}
						else 
						{
							$_SESSION['image_status'] = "tidak berhasil";
						}
						?>	
					</div>

					<div> <!-- Menampilkan Image Original -->
						<?php 
						if ($_SESSION['image_status'] = "berhasil") :?>		
						<img src="<?php echo $_SESSION['image_name_ori']; ?>" style="width: 300px"/>

					<?php endif; ?>
				</div> 
			</div>

			<div class="col-md-4"> <!-- Image Noise -->
				<label> GAMBAR NOISE </label>

				<?php

				if  ($_SESSION['image_status'] == "berhasil")  
				{
					if (isset($_POST["add"]))
					{
						$_SESSION['p']	= $_POST["prosentase"];

						$_SESSION['image_name_noise'] = "img-noise.".$_SESSION['image_ext'];
					} // if add

					// Image Noise Gaussian
					if (isset($_POST["gaussian"]))
					{
						noise_gaussian($_SESSION['image_name_ori'], $_SESSION['p'], $_SESSION['image_name_noise']);
						?>

						<?php  
					} // if gaussian 

					//Image Noise Saltpepper
					else if (isset($_POST["saltpepper"]))
					{

						noise_saltpepper($_SESSION['image_name_ori'], $_SESSION['p'], $_SESSION['image_name_noise']);
						?>

						<?php
						} // if saltpepper
						
					//Image Noise Speckle
						else if (isset($_POST["speckle"]))
						{
							noise_speckle($_SESSION['image_name_ori'], $_SESSION['p'], $_SESSION['image_name_noise']);
							?>

							<?php
						} // if speckle
					} //if image status
					else
					{
						echo "Masukkan Gambar";
					}
					?>

					<img src="<?php echo $_SESSION['image_name_noise']; ?>" style="width: 300px"/>

				</div>

				<div class="col-md-4"> 
					<label> GAMBAR REDUCE </label>

					<?php

				// Menampilkan Image Reduce
					if  ($_SESSION['image_status'] == "berhasil")  
					{
						if (isset($_POST['reduce']))
						{
							$_SESSION['image_name_reduce'] = "img-reduce.".$_SESSION['image_ext'];

							reduce($_SESSION['image_name_noise'], $_SESSION['image_name_reduce']);
							?>

							<img src="<?php echo $_SESSION['image_name_reduce']; ?>" style="width: 300px"/>

							<?php
						}  
					}
					else
					{
						echo "Masukkan Gambar";
					}
					?>
				</div>

			</div>
		</div>


		<?php

	//Validasi
		function validasi($value) 
		{
			if ($value >= 255) 
			{
				$hasil = 255;
			}
			elseif ($value <= 0) 
			{
				$hasil = 0;
			}
			else 
			{
				$hasil = $value;
			}

			return $hasil;
		}

	//Noise Gaussian
		function noise_gaussian($image_name, $p, $image_name_noise) 
		{

			if ($_SESSION['image_ext'] == "jpg" || $_SESSION['image_ext'] == "jpeg") 
			{
				$image_source = imagecreatefromjpeg($image_name);
			}
			elseif ($_SESSION['image_ext'] == "png") 
			{
				$image_source = imagecreatefrompng($image_name);
			}

			$image_lebar = imagesx($image_source);
			$image_tinggi = imagesy($image_source);



			for ($i=0; $i<$image_lebar; $i++) 
			{
				for ($j=0; $j<$image_tinggi; $j++) 
				{
					$a = rand(0, 255);
					$x = round($p/100*$a);

					$rgb = imagecolorat($image_source, $i, $j);

					$red =(($rgb >> 16) & 0xFF); 
					$green =(($rgb >> 8) & 0xFF);
					$blue = ($rgb & 0xFF);

					$val = imagecolorallocate($image_source, validasi($red+$x), validasi($green+$x), validasi($blue+$x));

					imagesetpixel($image_source, $i, $j, $val);
				}
			}

			if ($_SESSION['image_ext'] == "jpg" || $_SESSION['image_ext'] == "jpeg") {
				imagejpeg($image_source, $_SESSION['image_name_noise']);
			}
			elseif ($_SESSION['image_ext'] == "png") {
				imagepng($image_source, $_SESSION['image_name_noise']);
			}

			return $gray;
		}

// Noise Salt And Pepper
		function noise_saltpepper($image_name, $p, $image_name_noise) 
		{

			if ($_SESSION['image_ext'] == "jpg" || $_SESSION['image_ext'] == "jpeg") 
			{
				$image_source = imagecreatefromjpeg($image_name);
			}
			elseif ($_SESSION['image_ext'] == "png") 
			{
				$image_source = imagecreatefrompng($image_name);
			}

			$image_lebar = imagesx($image_source);
			$image_tinggi = imagesy($image_source);



			for ($i=0; $i<$image_lebar; $i++) 
			{
				for ($j=0; $j<$image_tinggi; $j++) 
				{
					$a = rand(0, 255);
					$x = round($p/100*$a);

					$rgb = imagecolorat($image_source, $i, $j);

					$red =(($rgb >> 16) & 0xFF); 
					$green =(($rgb >> 8) & 0xFF);
					$blue = ($rgb & 0xFF);

					$new_red = validasi($red+$x);
					$new_green = validasi($green+$x);
					$new_blue = validasi($blue+$x);

					if ($a<$p)
					{
						$new_red = 255;
						$new_blue = 255;
						$new_green = 255;
					}


					$val = imagecolorallocate($image_source, validasi($new_red), validasi($new_green), validasi($new_blue));

					imagesetpixel($image_source, $i, $j, $val);
				}
			}

			if ($_SESSION['image_ext'] == "jpg" || $_SESSION['image_ext'] == "jpeg") {
				imagejpeg($image_source, $_SESSION['image_name_noise']);
			}
			elseif ($_SESSION['image_ext'] == "png") {
				imagepng($image_source, $_SESSION['image_name_noise']);
			}

			return $gray;
		}

// Noise Speckle
		function noise_speckle($image_name, $p, $image_name_noise) 
		{

			if ($_SESSION['image_ext'] == "jpg" || $_SESSION['image_ext'] == "jpeg") 
			{
				$image_source = imagecreatefromjpeg($image_name);
			}
			elseif ($_SESSION['image_ext'] == "png") 
			{
				$image_source = imagecreatefrompng($image_name);
			}

			$image_lebar = imagesx($image_source);
			$image_tinggi = imagesy($image_source);



			for ($i=0; $i<$image_lebar; $i++) 
			{
				for ($j=0; $j<$image_tinggi; $j++) 
				{
					$a = rand(0, 255);
					$x = round($p/100*$a);

					$rgb = imagecolorat($image_source, $i, $j);

					$red =(($rgb >> 16) & 0xFF); 
					$green =(($rgb >> 8) & 0xFF);
					$blue = ($rgb & 0xFF);

					$new_red = validasi($red+$x);
					$new_green = validasi($green+$x);
					$new_blue = validasi($blue+$x);

					if ($a<$p)
					{
						$new_red = 0;
						$new_blue = 0;
						$new_green = 0;
					}


					$val = imagecolorallocate($image_source, validasi($new_red), validasi($new_green), validasi($new_blue));

					imagesetpixel($image_source, $i, $j, $val);
				}
			}

			if ($_SESSION['image_ext'] == "jpg" || $_SESSION['image_ext'] == "jpeg") {
				imagejpeg($image_source, $_SESSION['image_name_noise']);
			}
			elseif ($_SESSION['image_ext'] == "png") {
				imagepng($image_source, $_SESSION['image_name_noise']);
			}

			return $gray;
		}

//Reduce Image
		function reduce ($image_name_noise, $image_name_reduce)
		{

			if ($_SESSION['image_ext'] == "jpg" || $_SESSION['image_ext'] == "jpeg") 
			{
				$image_source = imagecreatefromjpeg($image_name_noise);
			}
			elseif ($_SESSION['image_ext'] == "png") 
			{
				$image_source = imagecreatefrompng($image_name_noise);
			}

			$image_lebar = imagesx($image_source);
			$image_tinggi = imagesy($image_source);

			for ($i=0; $i<$image_lebar; $i++) 
			{
				for ($j=0; $j<$image_tinggi; $j++) 
				{
					$pixel[0]=imagecolorat($image_source, $i-1, $j-1);
					$pixel[1]=imagecolorat($image_source, $i-1, $j );
					$pixel[2]=imagecolorat($image_source, $i-1, $j+1);
					$pixel[3]=imagecolorat($image_source, $i  , $j+1);
					$pixel[4]=imagecolorat($image_source, $i+1, $j+1);
					$pixel[5]=imagecolorat($image_source, $i+1, $j);
					$pixel[6]=imagecolorat($image_source, $i+1, $j-1);
					$pixel[7]=imagecolorat($image_source, $i  , $j-1);
					$pixel[8]=imagecolorat($image_source, $i  , $j);

					for($k=0; $k<9; $k++)
					{
						$red[$k]	= (($pixel[$k] >> 16) & 0xFF); 
						$green[$k]	= (($pixel[$k] >> 8) & 0xFF);
						$blue[$k]	= ($pixel[$k] & 0xFF);
					}

					sort($red);
					sort($green);
					sort($blue);

					$val = imagecolorallocate($image_source, $red[4], $green[4], $blue[4]);

					imagesetpixel($image_source, $i, $j, $val);
				}
			}

			if ($_SESSION['image_ext'] == "jpg" || $_SESSION['image_ext'] == "jpeg") 
			{
				imagejpeg($image_source, $_SESSION['image_name_reduce']);
			}
			elseif ($_SESSION['image_ext'] == "png") 
			{
				imagepng($image_source, $_SESSION['image_name_reduce']);
			}

			return $gray;
} // close reduce
?>



</center>
<script src="js/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>
<script type="text/javascript" src="js/Chart.min.js"></script>
</body>
</html>