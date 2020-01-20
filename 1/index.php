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

		<script>
			var WORKERS = 20;

			var camera, scene, renderer;
			var group;

			init();
			render();

			function initScene( width, height ) {

				camera = new THREE.PerspectiveCamera( 60, width / height, 1, 1000 );
				camera.position.z = 600;

				scene = new THREE.Scene();
				scene.background = new THREE.Color( 0xffffff );

				// materials

				var phongMaterial = new THREE.MeshPhongMaterial( {
					color: 0xffffff,
					specular: 0x222222,
					shininess: 150,
					vertexColors: THREE.NoColors,
					flatShading: false
				} );

				var phongMaterialBox = new THREE.MeshPhongMaterial( {
					color: 0xffffff,
					specular: 0x111111,
					shininess: 100,
					vertexColors: THREE.NoColors,
					flatShading: true
				} );

				var phongMaterialBoxBottom = new THREE.MeshPhongMaterial( {
					color: 0x666666,
					specular: 0x111111,
					shininess: 100,
					vertexColors: THREE.NoColors,
					flatShading: true
				} );

				var phongMaterialBoxLeft = new THREE.MeshPhongMaterial( {
					color: 0x990000,
					specular: 0x111111,
					shininess: 100,
					vertexColors: THREE.NoColors,
					flatShading: true
				} );

				var phongMaterialBoxRight = new THREE.MeshPhongMaterial( {
					color: 0x0066ff,
					specular: 0x111111,
					shininess: 100,
					vertexColors: THREE.NoColors,
					flatShading: true
				} );

				var mirrorMaterialFlat = new THREE.MeshPhongMaterial( {
					color: 0x000000,
					specular: 0xffffff,
					shininess: 10000,
					vertexColors: THREE.NoColors,
					flatShading: true
				} );
				mirrorMaterialFlat.mirror = true;
				mirrorMaterialFlat.reflectivity = 1;

				var mirrorMaterialFlatDark = new THREE.MeshPhongMaterial( {
					color: 0x000000,
					specular: 0xaaaaaa,
					shininess: 10000,
					vertexColors: THREE.NoColors,
					flatShading: true
				} );
				mirrorMaterialFlatDark.mirror = true;
				mirrorMaterialFlatDark.reflectivity = 1;

				var mirrorMaterialSmooth = new THREE.MeshPhongMaterial( {
					color: 0xffaa00,
					specular: 0x222222,
					shininess: 10000,
					vertexColors: THREE.NoColors,
					flatShading: false
				} );
				mirrorMaterialSmooth.mirror = true;
				mirrorMaterialSmooth.reflectivity = 0.3;

				var glassMaterialFlat = new THREE.MeshPhongMaterial( {
					color: 0x000000,
					specular: 0x00ff00,
					shininess: 10000,
					vertexColors: THREE.NoColors,
					flatShading: true
				} );
				glassMaterialFlat.glass = true;
				glassMaterialFlat.reflectivity = 0.5;

				var glassMaterialSmooth = new THREE.MeshPhongMaterial( {
					color: 0x000000,
					specular: 0xffaa55,
					shininess: 10000,
					vertexColors: THREE.NoColors,
					flatShading: false
				} );
				glassMaterialSmooth.glass = true;
				glassMaterialSmooth.reflectivity = 0.25;
				glassMaterialSmooth.refractionRatio = 0.6;

				//

				group = new THREE.Group();
				scene.add( group );

				// geometries

				var sphereGeometry = new THREE.SphereBufferGeometry( 100, 16, 8 );
				var planeGeometry = new THREE.BoxBufferGeometry( 600, 5, 600 );
				var boxGeometry = new THREE.BoxBufferGeometry( 100, 100, 100 );

				// Sphere

				var sphere = new THREE.Mesh( sphereGeometry, phongMaterial );
				sphere.scale.multiplyScalar( 0.5 );
				sphere.position.set( - 50, - 250 + 5, - 50 );
				group.add( sphere );

				var sphere2 = new THREE.Mesh( sphereGeometry, mirrorMaterialSmooth );
				sphere2.scale.multiplyScalar( 0.5 );
				sphere2.position.set( 175, - 250 + 5, - 150 );
				group.add( sphere2 );

				// Box

				var box = new THREE.Mesh( boxGeometry, mirrorMaterialFlat );
				box.position.set( - 175, - 250 + 2.5, - 150 );
				box.rotation.y = 0.5;
				group.add( box );

				// Glass

				var glass = new THREE.Mesh( sphereGeometry, glassMaterialSmooth );
				glass.scale.multiplyScalar( 0.5 );
				glass.position.set( 75, - 250 + 5, - 75 );
				glass.rotation.y = 0.5;
				scene.add( glass );

				// bottom

				var plane = new THREE.Mesh( planeGeometry, phongMaterialBoxBottom );
				plane.position.set( 0, - 300 + 2.5, - 300 );
				scene.add( plane );

				// top

				var plane = new THREE.Mesh( planeGeometry, phongMaterialBox );
				plane.position.set( 0, 300 - 2.5, - 300 );
				scene.add( plane );

				// back

				var plane = new THREE.Mesh( planeGeometry, phongMaterialBox );
				plane.rotation.x = 1.57;
				plane.position.set( 0, 0, - 300 );
				scene.add( plane );

				var plane = new THREE.Mesh( planeGeometry, mirrorMaterialFlatDark );
				plane.rotation.x = 1.57;
				plane.position.set( 0, 0, - 300 + 10 );
				plane.scale.multiplyScalar( 0.85 );
				scene.add( plane );

				// left

				var plane = new THREE.Mesh( planeGeometry, phongMaterialBoxLeft );
				plane.rotation.z = 1.57;
				plane.position.set( - 300, 0, - 300 );
				scene.add( plane );

				// right

				var plane = new THREE.Mesh( planeGeometry, phongMaterialBoxRight );
				plane.rotation.z = 1.57;
				plane.position.set( 300, 0, - 300 );
				scene.add( plane );

				// light

				var intensity = 70000;

				var light = new THREE.PointLight( 0xffaa55, intensity );
				light.position.set( - 200, 100, 100 );
				light.physicalAttenuation = true;
				scene.add( light );

				var light = new THREE.PointLight( 0x55aaff, intensity );
				light.position.set( 200, 100, 100 );
				light.physicalAttenuation = true;
				scene.add( light );

				var light = new THREE.PointLight( 0xffffff, intensity * 1.5 );
				light.position.set( 0, 0, 300 );
				light.physicalAttenuation = true;
				scene.add( light );

			}

			function init() {



				//

				initScene( window.innerWidth, window.innerHeight );

				//

				renderer = new THREE.RaytracingRenderer( {
					workers: WORKERS,
					workerPath: 'js/RaytracingWorker.js',
					blockSize: 64
				} );
				renderer.setSize( window.innerWidth, window.innerHeight );
				document.body.appendChild( renderer.domElement );

					
					//renderer.setWorkers( WORKERS );
				window.addEventListener( 'resize', function () {

					renderer.setSize( innerWidth, innerHeight );

				} );

			}


			function render() {

				renderer.render( scene, camera );

			}


		</script>

	</body>
</html>