export const categoryList = [
	{ name: "Tables and Chairs", id: 0 },
	{ name: "Stage-Dance-Theme", id: 1 },
	{ name: "AV Equiement", id: 2 },
	{ name: "Outerdoor and Exhibition", id: 3 },
	{ name: "Basic Shape and Building", id: 4 },
	{ name: "Shape", id: 5 },
];

export const Mode = {
	INSIDE: "mode.inside",
	OUTSIDE: "mode.outside",
	DOLLHOUSE: "mode.dollhouse",
	FLOORPLAN: "mode.floorplan",
	TRANSITIONING: "mode.transitioning",
};

export const INIT_SCENE_DATA = {
	version: "1.0",
	payload: {
		objects: [
			{
				name: "dimension",
				position: { x: 0, y: 0, z: 0 },
				rotation: { x: 0, y: 0, z: 0 },
				scale: { x: 1, y: 1, z: 1 },
				components: [
					{
						type: "mp.dimension",
					},
					{
						type: "mp.point",
					},
				],
			},
		],
	},
};

export const environments = [
	{
		id: "",
		name: "None",
		path: null,
	},
	{
		id: "neutral", // THREE.RoomEnvironment
		name: "Neutral",
		path: null,
	},
	{
		id: "venice-sunset",
		name: "Venice Sunset",
		path: "https://storage.googleapis.com/donmccurdy-static/venice_sunset_1k.exr",
		format: ".exr",
	},
	{
		id: "footprint-court",
		name: "Footprint Court (HDR Labs)",
		path: "https://storage.googleapis.com/donmccurdy-static/footprint_court_2k.exr",
		format: ".exr",
	},
];
