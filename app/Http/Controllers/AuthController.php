<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use Utils\Helper\FightGame;
use App\Models\GameLogs;

class AuthController extends Controller {

    public $game;

    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->game = new FightGame();
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {
        $validator = Validator::make($request->all(), ['email' => 'required|email', 'password' => 'required|string|min:6',]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        if (!$token = auth()->attempt($validator->validated())) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->createNewToken($token);
    }

    /**
     * Register a User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request) {
        $validator = Validator::make($request->all(), ['full_name' => 'required|string|between:2,100',
                    'email' => 'required|string|email|max:100|unique:users', 'password' => 'required|string|min:6',
                        //'password' => 'required|string|confirmed|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::create(array_merge($validator->validated(), ['password' => bcrypt($request->password)]));

        if ($request->hasFile('avathar')) {
            $path = $request->file('avathar')->store("uploads/user");
            $user->avathar = $path;
            $user->save();
        }

        return response()->json(['message' => 'User successfully registered', 'user' => $user], 201);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();

        return response()->json(['message' => 'User successfully signed out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh() {
        return $this->createNewToken(auth()->refresh());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function userProfile() {
        $user = auth()->user();

        return response()->json($user);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token) {
        return response()->json(['access_token' => $token, 'token_type' => 'bearer', 'expires_in' => auth()->factory()->getTTL() * 60]);
    }

    public function initPlayers() {
        $user = auth()->user();

        $this->game->initThePlayers($user['full_name'], 'Dragon');
        array_push($this->game->recorder, '3...2...1... GO!');
        $data = array('user_id' => $user['id'], 'isPlayerOnesTurn' => $this->game->isPlayerOnesTurn, 'isOver' => $this->game->isOver(),
            'playerOneLifeValue' => $this->game->playerOne->getLifeValue(),
            'playerTwoLifeValue' => $this->game->playerTwo->getLifeValue(),
            'playerOneName' => $this->game->playerOne->getName(),
            'playerTwoName' => $this->game->playerTwo->getName(),
            'recorder' => $this->game->recorder);

        return response()->json($data);
    }

    public function playTurn(Request $request) {
        $user = auth()->user();
        $winner = '';

        $this->game->isPlayerOnesTurn = $request->input('isPlayerOnesTurn');
        $this->game->playerOne->setLifeValue($request->input('playerOneLifeValue'));
        $this->game->playerTwo->setLifeValue($request->input('playerTwoLifeValue'));
        $this->game->playerOne->setName($request->input('playerOneName'));
        $this->game->playerTwo->setName($request->input('playerTwoName'));
        $this->game->recorder = $request->input('recorder');

        if (!$this->game->isOver()) {
            $this->game->playTurn();
            $this->game->playTurn();
        } else {
            if ($this->game->playerTwo->getLifeValue() > $this->game->playerOne->getLifeValue()) {
                $winner = $this->game->playerTwo->getName();
            } else {
                $winner = $this->game->playerOne->getName();
            }
        }
        $data = array('user_id' => $user['id'], 'isPlayerOnesTurn' => $this->game->isPlayerOnesTurn, 'isOver' => $this->game->isOver(),
            'playerOneLifeValue' => $this->game->playerOne->getLifeValue(),
            'playerTwoLifeValue' => $this->game->playerTwo->getLifeValue(),
            'playerOneName' => $this->game->playerOne->getName(),
            'playerTwoName' => $this->game->playerTwo->getName(),
            'recorder' => $this->game->recorder,
            'winner' => $winner);

        return response()->json($data);
    }

    public function saveGameresult(Request $request) {
        $gameId = "GM-" . uniqid();
        $user_id = $request->input('user_id');
        $playerOneName = $request->input('playerOneName');
        $playerTwoName = $request->input('playerTwoName');
        $recorder = $request->input('recorder');
        $playerOneLifeValue = $request->input('playerOneLifeValue');
        $playerTwoLifeValue = $request->input('playerTwoLifeValue');
        $result = ($playerOneLifeValue > $playerTwoLifeValue) ? 'Won' : 'Lost';

        $data = array('game_id' => $gameId, 'user_id' => $user_id, 'playerOneName' => $playerOneName, 'playerTwoName' => $playerTwoName,
            'recorder' => json_encode($recorder), 'playerOneLifeValue' => $playerOneLifeValue, 'playerTwoLifeValue' => $playerTwoLifeValue, 'result' => $result);
        $result = GameLogs::create($data);

        return response()->json($result);
    }

    public function mygames() {
        $user = auth()->user();
        $result = GameLogs::where(['user_id' => $user['id']])->orderBy('created_at', 'desc')->get();
        return response()->json($result);
    }

    public function mygameinfo(Request $request) {
        $user = auth()->user();
        $gameId = $request->input('id');
        $result = GameLogs::where(['id' => $gameId, 'user_id' => $user['id']])->first();
        $result['recorder'] = json_decode($result['recorder'], true);
        return response()->json($result);
    }

}
