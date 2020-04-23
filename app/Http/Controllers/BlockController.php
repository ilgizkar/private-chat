<?php

namespace App\Http\Controllers;

use App\Events\BlockEvent;
use App\Models\Session;
use Illuminate\Http\Request;

class BlockController extends Controller
{
    public function block(Session $session)
    {
        $session->block = true;
        $session->blocked_id = auth()->id();
        $session->save();

        broadcast(new BlockEvent($session->id, true));

        return response(null, 201);
    }

    public function unblock(Session $session)
    {
        $session->block = false;
        $session->blocked_id = null;
        $session->save();

        broadcast(new BlockEvent($session->id, false));

        return response(null, 201);
    }
}
