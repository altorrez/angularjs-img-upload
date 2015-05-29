<?php 

$router->post('/images', function() use ($request, $response) {

	$fs = new albus\Core\Filesystem();
	$filename = $fs->saveImage('image');
	if(!$filename) {
		$response->error();
		echo 'Could not save image: ' . $fs->getMessage();
		return;
	}

	$response->setContentType('application/json');
	$image = array('src' => $request->getURLPath() . $filename, 'name' => $filename);
	echo json_encode($response->created($image), JSON_PRETTY_PRINT);
});

// If running off database db reference would be more appropriate and can restrict access based off user roles
$router->get('/images/:imagename', function($imagename) use ($request, $response) {

	$fs = new albus\Core\Filesystem();
	$image = $fs->getFile($imagename);
	if(!$image) {
		$response->setContentType('text/html');
		$response->notfound();
		echo 'Image not found';
		return;
	}

	if(isset($_GET['thumb'])) {
		$response->setContentType('image/jpeg');
		$fs->getThumb($image, $imagename);
		return;
	}else {
		$response->setContentType('image/jpeg');
		echo $image;
	}
});

// Optional: add pagnation here - 
// If running off database db reference would be more appropriate and can restrict access based off user roles
$router->get('/images', function() use ($request, $response) {
	$response->setContentType('application/json');
	$fs = new albus\Core\Filesystem();
	$dir = $fs->listDir();

	// Remove 'index.html'
	$r_index = array_search('index.html', $dir);
	unset($dir[$r_index]);
	$dir = array_values($dir);

	// Prepend full url to access image
	$url = $request->getURLPath();
	$dir_len = count($dir);
	for($i = 0; $i < $dir_len; $i++)
		$dir[$i] = array('src' => $url . $dir[$i] . '?thumb', 'name' => $dir[$i]);

	echo json_encode($dir, JSON_PRETTY_PRINT);
});
