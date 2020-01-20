<!DOCTYPE html>
<html lang="en">
	<head>
		<title>three.js - raytracing renderer with web workers</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0">
		<link type="text/css" rel="stylesheet" href="main.css">
	</head>
	<body>

		<script src="js/three.min.js"></script>
		<script src="js/RaytracingRenderer.js"></script>

		<script src="js/dp/OrbitControls.js"></script>



<script>
var container, stats;
var camera, scene, renderer, controls;
var raycaster = new THREE.Raycaster();
var mouse = new THREE.Vector2();
var selectedObjects = [];
var composer, outlinePass;

var params = {
	edgeStrength: 3.0,
	edgeGlow: 0.0,
	edgeThickness: 1.0,
	pulsePeriod: 0,
	rotate: false,
	usePatternTexture: false
};
// Init gui

init();
animate();


function init() 
{
	container = document.createElement( 'div' );
	document.body.appendChild( container );
	var width = window.innerWidth;
	var height = window.innerHeight;
	
	scene = new THREE.Scene();
	scene.background = new THREE.Color( 0xffffff );	
	
	if(1==2)
	{
		camera = new THREE.PerspectiveCamera( 45, width / height, 0.1, 100 );
		camera.position.set( 0, 0, 8 );	
		
		renderer = new THREE.WebGLRenderer();
		renderer.setSize( width, height );
		document.body.appendChild( renderer.domElement );
		
		controls = new THREE.OrbitControls( camera, renderer.domElement );
		controls.minDistance = 5;
		controls.maxDistance = 20;
		controls.enablePan = false;
		controls.enableDamping = true;
		controls.dampingFactor = 0.05;		
		
	}

	//
	
	scene.add( new THREE.AmbientLight( 0xaaaaaa, 0.2 ) );
				var intensity = 70000;

				var light = new THREE.PointLight( 0xffffff, intensity );
				light.position.set( - 200, 100, 100 );
				//light.physicalAttenuation = true;
				scene.add( light );

				var light = new THREE.PointLight( 0xffffff, intensity );
				light.position.set( 200, 100, 100 );
				//light.physicalAttenuation = true;
				scene.add( light );

				var light = new THREE.PointLight( 0xffffff, intensity * 1.5 );
				light.position.set( 0, 0, 300 );
				//light.physicalAttenuation = true;
				scene.add( light );
	// model

	//
	var geometry = new THREE.BoxBufferGeometry( 3, 3, 3 );
	for ( var i = 0; i < 20; i ++ ) {
		var material = new THREE.MeshLambertMaterial();
		material.color.setHSL( Math.random(), 1.0, 0.3 );
		var mesh = new THREE.Mesh( geometry, material );
		mesh.position.x = Math.random() * 4 - 2;
		mesh.position.y = Math.random() * 4 - 2;
		mesh.position.z = Math.random() * 4 - 2;

		mesh.scale.multiplyScalar( Math.random() * 0.3 + 0.1 );
		scene.add( mesh );
	}


	//window.addEventListener( 'resize', onWindowResize, false );
	//window.addEventListener( 'mousemove', onTouchMove );
	//window.addEventListener( 'touchmove', onTouchMove );
	
	function onTouchMove( event ) {
		var x, y;
		if ( event.changedTouches ) {
			x = event.changedTouches[ 0 ].pageX;
			y = event.changedTouches[ 0 ].pageY;
		} else {
			x = event.clientX;
			y = event.clientY;
		}
		mouse.x = ( x / window.innerWidth ) * 2 - 1;
		mouse.y = - ( y / window.innerHeight ) * 2 + 1;
	}
	
	initRaytracing();
}


function initRaytracing() 
{	
	renderer = new THREE.RaytracingRenderer
	({
		workers: 5,
		workerPath: 'js/RaytracingWorker.js',
		blockSize: 64
	});
	
	renderer.setSize( window.innerWidth, window.innerHeight );
	document.body.appendChild( renderer.domElement );	
	
	camera = new THREE.PerspectiveCamera( 45, window.innerWidth / window.innerHeight, 0.1, 100 );
	camera.position.set( 0, 0, 8 );	
}


function onWindowResize() {
	var width = window.innerWidth;
	var height = window.innerHeight;
	camera.aspect = width / height;
	camera.updateProjectionMatrix();
	renderer.setSize( width, height );
}


function animate() {
	//requestAnimationFrame( animate );

	renderer.render( scene, camera );
}

</script>

	</body>
</html>