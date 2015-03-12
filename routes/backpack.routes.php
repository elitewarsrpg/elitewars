<?php

// Upon exiting the backpack, a div is being created on the top of the page for some odd reason.. 

$app->get('/backpack(/)(/:which)(/)(/:action)(/)(/:itemid)', $isLoggedIn(), function($which = '', $action = '', $itemid = '') use ($app) {
	
	$userid = Session::get('userid');
	$which = (!$which) ? 'items' : $which;
	
	// Items in the users backpack, must be un-equipped, un-dropped and un-vaulted.
	$items = $app->capsule->table('items')
            ->join('useritems', 'items.itemid', '=', 'useritems.itemid')
            ->join('users', 'users.userid', '=', 'useritems.userid')
            ->where('useritems.equipped', '=', 0)
            ->where('useritems.dropped', '=', 0)
            ->where('useritems.vault', '=', 0)
            ->where('useritems.userid', '=', $userid)
            ->where('useritems.type', '=', $which)
	    ->select('*')
            ->get();
             
             
       	// items in the users backpack. Loops through until no more items are found.    	
       	$bpCount = $app->capsule->table('useritems')->where('userid', '=', $userid)->count();     	
       	
       	$i = 0;
       	while ($i < 25) {
       		$i++;
       	       	
	       	// leftover backpack space.  (builds the leftover layout)  	
	       	$leftoverArr = array();
	       	while ($i != 25 OR $i < 25) {	       		
	       		$leftoverArr[] = array();
	       		$i++;
	       		
	       	}
	}
	
	// If theres no items in the users backpack.
	$i = 0;
	$emptyArr = array();
	while ($i < 25) {
		$emptyArr[] = array();
		++$i;
	}
	       
        // Render the backpack with data.
	$app->render('backpack.twig', array(
		'userid' => $userid,
		'which' => $which,
		'items' => $items,
		'leftoverArr' => $leftoverArr,
		'emptyArr' => $emptyArr
		)
	);
});


// Very simple equip action, ajax loads this route.
// This will get redone, and therefore become deprecated.
$app->get('/itemactions/equip/:itemid', $isLoggedIn(), function($itemid) use ($app) {

	$userid = Session::get('userid');
	
	if (!$itemid) {
		exit;
	}
	
	// Equip the item. (test)
	$app->capsule->table('useritems')
		->where('useritemid', $itemid)
		->where('userid', $userid)
		->update(['equipped' => 1]);
		
	// and it works.
});	
	
	
// The users equipment. 
// This route is loaded through createWindow('Equipment', 'eqWin', 314, '100px');
// TODO: Add orbs, and possible find a less cheap solution.
$app->get('/equipment', $isLoggedIn(), function() use ($app) {
	
	$userid = Session::get('userid');
	$slots = ['head', 'necklace', 'weapon', 'body', 'shield', 'pants', 'belt', 'ring', 'boots'];

	// get the users slot ids.
	$users = $app->capsule->table('users')
		->where('userid', '=', $userid)
		->get($slots);
	

	/*
	// Possibly a better solution? Just needs a bit more logic.
	$query = $app->capsule->table('useritems')
		->join('items', function($join)
	        {
	            $join->on('useritems.itemid', '=', 'items.itemid');
	        })
		->whereIn('useritems.slot', $slots)		
		->where('userid', $userid)
		->where('equipped', 1)
		->get();
		
	foreach ($query as $equip) {
		var_dump($equip);
	}
	*/
	

	
	// image function for now.
	function itemImage($itemid, $app) {
		$item = $app->capsule->table('useritems')
			->join('items', 'useritems.itemid', '=', 'items.itemid')
			->where('useritems.useritemid', '=', $itemid)
			->get();
			
		foreach ($item as $image) {
			return $image->image;
		}
	}
	

	// Prepare the equipment for the view.
	foreach ($users as $user) {
	
		foreach ($slots as $slot) {
		
			if ($user->$slot === 0) {
				$$slot = []; // define/create the array variable.
				continue; // $slot not equipped, re-loop, try another slot.
			}
		
			switch ($slot) {
			
				case 'head':
					$head[] = array('itemid' => $user->head, 'image' => itemImage($user->head, $app));
					break;
					
				case 'necklace':
					$necklace[] = array('itemid' => $user->necklace, 'image' => itemImage($user->necklace, $app));
					break;
					
				case 'weapon':
					$weapon[] = array('itemid' => $user->weapon, 'image' => itemImage($user->weapon, $app));
					break;
					
				case 'body':
					$body[] = array('itemid' => $user->body, 'image' => itemImage($user->body, $app));
					break;
					
				case 'shield':
					$shield[] = array('itemid' => $user->shield, 'image' => itemImage($user->shield, $app));
					break;
					
				case 'pants':
					$pants[] = array('itemid' => $user->pants, 'image' => itemImage($user->pants, $app));
					break;
					
				case 'belt':
					$belt[] = array('itemid' => $user->belt, 'image' => itemImage($user->belt, $app));
					break;
					
				case 'ring':
					$ring[] = array('itemid' => $user->ring, 'image' => itemImage($user->ring, $app));
					break;
					
				case 'boots':
					$boots[] = array('itemid' => $user->boots, 'image' => itemImage($user->boots, $app));
					break;
					
			}
		
		}
	}	
	
	// Render the equipment with data.
	$app->render('equipment.twig', array(
		'weaponArr' => $weapon,
		'neckArr' => $necklace,
		'headArr' => $head,
		'bodyArr' => $body,
		'shieldArr' => $shield,
		'pantsArr' => $pants,
		'beltArr' => $belt,
		'ringArr' => $ring,
		'bootsArr' => $boots
		)
	);
	
});
	
	
	
	
