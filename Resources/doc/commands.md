# Commands

## Start a new game
```sh
$ app/console windmill:game:new
###############################################
#                                             #
#    Welcome to the Windmill chess engine!    #
#                                             #
###############################################
Please enter the name of the player controlling white: Cas
Will this player be human-controlled? y
Please enter the name of the player controlling black: Computer
Will this player be human-controlled? n
Started game with ID 12345

8 ║♜ ♞ ♝ ♛ ♚ ♝ ♞ ♜
7 ║♟ ♟ ♟ ♟ ♟ ♟ ♟ ♟
6 ║… … … … … … … …
5 ║… … … … … … … …
4 ║… … … … … … … …
3 ║… … … … … … … …
2 ║♙ ♙ ♙ ♙ ♙ ♙ ♙ ♙
1 ║♖ ♘ ♗ ♕ ♔ ♗ ♘ ♖
  ╚═══════════════
   a b c d e f g h

Cas's turn to move.
Please enter the position to move to:
```


## Load an existing game
```sh
$ app/console windmill:game:load 12345
###############################################
#                                             #
#    Welcome to the Windmill chess engine!    #
#                                             #
###############################################

8 ║♜ … ♝ ♛ ♚ ♝ … ♜
7 ║… ♟ … ♟ ♟ ♟ ♟ ♟
6 ║… … ♞ … … … … ♞
5 ║♟ … … ♙ … … … …
4 ║… ♙ … … … … … …
3 ║… … … … … … … …
2 ║… … ♙ … ♙ ♙ ♙ ♙
1 ║♖ ♘ ♗ ♕ ♔ ♗ ♘ ♖
  ╚═══════════════
   a b c d e f g h

Cas's turn to move.
Please enter the position to move to:
```
