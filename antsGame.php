<?php
/**
 * This php file implements a solution to the Question 1: Marching Ants from the assignment.
 * A string is received from an incoming GET request using the 'filter_input' function to ensure security as the input is originating from the client. 
 * The function 'marching_ants' is where the logic for simulating the ants is implemented. It will take a single parameter 'ants_string' which is retrieved 
 * from the user, and first check that the string only contains 'R, B, or X', and then convert the string into an array. By first determining the number of each
 * ant in the array, and then continuously looping over the array of ants simulating their movement and checking for any encounters, the output is determined by
 * whether or not the ants have reached the opposing ants colony, if all the ants will die meaning neither win, or if black or red win.
 * 
 * @author Brendan Dileo
 * @version  1.0
 * @package serverToClient
 */

$antsString = filter_input(INPUT_GET, 'ants', FILTER_SANITIZE_STRING); // Ensures input received from get request does not include harmful code.

/**
 * This function implements the logic that will simulate the marching ants, moving and battling.
 * The function takes a single parameter '$antsString' which holds the string representation of the ants.
 * First the string is trimmed of any whitespace, and then checked to see if the input was empty, if it is, 
 * then an error message is displayed. Next another if statement is used with a regular expression to see if
 * the string contains any characters that are not in the set of 'X, R, or B'. If the user has entered anything 
 * that isn't in that set of characters, they are displayed an error message. Once the string has been validated,
 * it is split into an array where each element holds either color ant, or an empty spot 'X'. A foreach loop is 
 * then used to count the number of each ants in the string (Red and Black). Once the number of ants has been 
 * established, and have been split into an array, a do while loop is used to continuously check for encounters 
 * between ants, controlled by a variable indicating whether or not encounters are still taking place. I have used
 * a temporary array that is a copy of the initial array, as at first modifying the actual array lead to unexpected
 * behavior in determining outcomes, as it was skipping some iterations. Inside the loop, two for loops are used 
 * to simulate the battle and check for encounters between ants. The first loop tracks Black ants traveling right,
 * checking for encounters with red ants, and the second for loop checks for Red ants moving left, checking for 
 * encounters with Black ants. Instead of modifying the array and checking that array, the initial array is modified,
 * but the checks happen on the copy of the array which holds the initial state so no iterations are skipped. If any 
 * encounters have taken place, both ants who have died will have their spots replaced with an 'X', and the number 
 * of ants are decremented to reflect the ants have encountered each other. A foreach loop is used to add surviving ants to
 * a new array containing only surviving ants, which is then reassigned to the initial array to then re check for any encounters.
 * This will continue to occur until the do while control variable is false, which means no more encounters can take place.
 * The output of the function is determined by the array holding surviving ants, as an if statement checks the positions of
 * the ants, and the number of ants on each side.
 * 
 * @param string $antsString A string representation of the marching ants, consisting of 'X, R, or B'.
 * @return void The function echos (displays) the result of the marching ants.
 */
function marchingAnts($antsString) {
    
    $antsString = trim($antsString, " "); // Trims whitespace from the string.
    
    if ($antsString == "") { // Checks for empty input. 
        echo "Error! You did not enter anything!";
        return;
    }

    if (preg_match("/[^XBR]/", $antsString)) { // Checks if the string contains any characters that are not 'X, B, or R'.
        echo "Error! You can only enter 'X', 'R', or 'B'!";     
        return;
    }

    $ants = str_split($antsString); // Splits the string into an array of ants (characters).
    $redAnts = 0;
    $blackAnts = 0;
    foreach ($ants as $ant) {
        if ($ant == 'R') {
            $redAnts++;
        }
        else if ($ant == 'B') {
            $blackAnts++;
        }
    }

     do {
        $hasEncounters = false; // Flag that tracks if ants have encountered each other.
        $antsTemp = $ants; // Holds a copy of the initial array.
        
        // Simulates Black Ants moving right.
        for ($i = 0; $i < count($antsTemp) - 1; $i++) { // Iterates up to the initial length of the array in the temporary array.
            if ($antsTemp[$i] == 'B' && $antsTemp[$i + 1] == 'R') { // Checks for positions of the initial array in the temporary array.
                $ants[$i] = 'X'; // Changes element of actual array.
                $ants[$i + 1] = 'X'; // Changes element of actual array.
                $blackAnts--;
                $redAnts--;
                $hasEncounters = true;
            }
        }

        $antsTemp = $ants;
        // Simulates Red Ants moving left.
        for ($i = count($antsTemp) - 1; $i >= 1; $i--) { // Iterates up to the initial length of the array in the temporary copy.
            if ($antsTemp[$i] == 'R' && $antsTemp[$i - 1] == 'B') {  // Checks for positions of the initial array in the temporary array copy.
                $ants[$i] = 'X'; // Changes element of actual array.
                $ants[$i - 1] = 'X'; // Changes element of actual array.
                $blackAnts--;
                $redAnts--;
                $hasEncounters = true;
            }
        }

        $antsSurvived = array();
        foreach ($ants as $ant) {
            if ($ant != 'X') {
                    $antsSurvived[] = $ant; // Stores all of the remaining ants in a new array $antsSurvived.
        }
        }

        $ants = $antsSurvived; // Reassigns surviving ants to the original array so the next set of encounters can be checked effectively.
    } while ($hasEncounters);
    
    // Determines outcome based on the ants that have survived.
    if ($antsSurvived) { // Checks to see if array is empty (No 'R' or 'B')
        if ($antsSurvived[0] == 'R' && end($antsSurvived) == 'B') { // Checks if a Red ant has reached the left, and a Black ant has reached the right.
            echo "M.A.D!"; // This results in M.A.D, Red ant moved all the way left, Black ant moved all the way right.
        } else {
            if ($blackAnts > $redAnts) { // Checks if there are more Black ants than Red, resulting in Black winning after simulating encounters.
                echo " Black Wins!";
            } else if ($redAnts > $blackAnts) { // Checks if there are more Red ants than Black, resulting in Red winning after simulating encounters.
                echo "Red Wins!";
            } else { // If neither color of ant has reached the other colony, and there are equal number of ants, neither side wins.
                echo "Neither!";
            }
        }
    } else { // If the array is empty (Only 'X'), neither side wins.
        echo "Neither!";
    }
}

marchingAnts($antsString); // Calls function.
?>