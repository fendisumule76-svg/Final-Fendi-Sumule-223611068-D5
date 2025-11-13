<?php
namespace Src\Controllers;

use Src\Repositories\UserRepository;
use Src\Validation\Validator;

class UserController extends BaseController
{
    public function index()
    {
        $p = (int)($_GET['page'] ?? 1);
        $per = (int)($_GET['per_page'] ?? 10);
        $repo = new UserRepository($this->cfg);
        $this->ok($repo->paginate(max(1, $p), min(100, max(1, $per))));
    }

    public function show($id)
    {
        $repo = new UserRepository($this->cfg);
        $u = $repo->find((int)$id);
        $u ? $this->ok($u) : $this->error(404, 'User not found');
    }

    public function store()
    {
        $in = json_decode(file_get_contents('php://input'), true) ?? [];
        $v = Validator::make($in, [
            'name' => 'required|min:3|max:100',
            'email' => 'required|email|max:150',
            'password' => 'required|min:6|max:150',
            'role' => 'enum:user,admin'
        ]);

        if ($v->fails()) {
            return $this->error(422, 'Validation error', $v->errors());
        }

        $hash = password_hash($in['password'], PASSWORD_DEFAULT);
        $repo = new UserRepository($this->cfg);

        try {
            $this->ok(
                $repo->insert( 
                    $in['name'],
                    $in['email'],
                    $hash,
                    $in['role'] ?? 'user'
                ),
                201
            );
        } catch (\Throwable $e) {
            $this->error(400, 'Create failed', ['details' => $e->getMessage()]);
        }
    }

    public function update($id)
    {
        $in = json_decode(file_get_contents('php://input'), true) ?? [];
        $v = Validator::make($in, [
            'name' => 'required|min:3|max:100',
            'email' => 'required|email|max:150',
            'role' => 'enum:user,admin'
        ]);
        if ($v->fails()) {
            return $this->error(422, 'Validation error', $v->errors());
        }

        $repo = new UserRepository($this->cfg);
        $this->ok($repo->update((int)$id, $in['name'], $in['email'], $in['role']));
    }

    public function destroy($id)
    {
        $repo = new UserRepository($this->cfg);
        $ok = $repo->delete((int)$id);
        $ok ? $this->ok(['deleted' => true]) : $this->error(400, 'Delete failed');
    }
}
