<?php

/**
 * Attack script - Loop through, creating an array for the attack to be json_encoded for use in the attack animation.
 * TODO: 
 * - Turn this into a function.
 * - Add elemental damages.
 * - Keep this to one script, to allow it to configure PVP and PVM.
 */

// The actual attack.
$app->get('/attack', $isLoggedIn(), function() use ($app) {
    
    // Example array of attacker.
    $attacker = array(
        'username' => 'attacker',
        'attack' => 10,
        'start_hp' => 100,
        'block' => 5,
        'critical' => 5
        );
    
    // Example array of defender.
    $defender = array(
        'username' => 'defender',
        'attack' => 10,
        'start_hp' => 90,
        'block' => 0,
        'critical' => 0
        );
        
    $player_hp = 100;
    $target_hp = 90;
        

    $attackArr = array();
    $winner = 0;
    while (!$winner) 
    {
       
        static $turn = 'player';
        if ($turn === 'player') {
            
            // TODO: Ternary ops are great and all -
            // but to keep everything more organized and readable maybe if/else will be a better solution?

            $playerAttack = 0; // Default to 0 - incase it gets confused :o

            $block = ($defender['block'] >= rand(1,100)) ? 1 : 0; // Block hit
            $critical = ($attacker['critical'] >= rand(1,100)) ? 1 : 0; // Critical hit
            
            // Set the total attack
            $playerAttack = ($critical === 0) ? $attacker['attack'] : $attacker['attack']+5;
            
            $attackArr[] = array(
                'message' => ($block === 0) ? 'attacker hits for ' . $playerAttack . (($critical === 1) ? '&nbsp;CRITICAL' : '') : 'defender blocked',
                'attack' => ($block === 0) ? $playerAttack : 'Block',
                'player_hp' => $player_hp,
                'target_hp' => $target_hp,
                'turn' => 'player',
                'type' => 'attack'
                );
        
        }

        $target_hp -= ($block === 0) ? $playerAttack : 0;

        if ($target_hp < 1) {
            $winner = 0;
            $attackArr[] = array(
                'message' => 'attacker wins!',
                'attack' => '',
                'player_hp' => $player_hp,
                'target_hp' => ($target_hp < 0) ? 0 : $target_hp,
                'turn' => '',
                'type' => 'win'
                );
            break;
        }
        
        $turn = 'target';
        if ($turn === 'target') {
 
            $block = ($attacker['block'] >= rand(1,100)) ? 1 : 0;

            $attackArr[] = array(
                'message' => ($block === 0) ? 'defender hits for '. $defender['attack'] : 'attacker blocked',
                'attack' => ($block === 0) ? $defender['attack'] : 'Block',
                'player_hp' => $player_hp,
                'target_hp' => $target_hp,
                'turn' => 'target',
                'type' => 'attack'
                );
        }
        
        $player_hp -= ($block === 0) ? $defender['attack'] : 0;
   
        if ($player_hp < 1) {            
            $winner = 1;
            $attackArr[] = array(
                'message' => 'defender wins!',
                'attack' => '',
                'player_hp' => ($player_hp < 0) ? 0 : $player_hp,
                'target_hp' => $target_hp,
                'turn' => '',
                'type' => 'win'
                );
            break;
        }
        
        $turn = 'player'; // Re-loop, noones dead, it's the players turn again.
    }
    
    // Render the attack page, with the data.
    $app->render('attack.twig', array(
        'attackArr' => $attackArr, 
        'playerArr' => $attacker,
        'targetArr' => $defender,
        'winner' => $winner,
        )
    );
});
