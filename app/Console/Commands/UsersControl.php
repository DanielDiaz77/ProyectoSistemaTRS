<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class UsersControl extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:control';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Commando para desactivar/activar el acceso a los usuarios';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $vendedores = User::select('id','condicion')
        ->where([['last_act','!=',null],['idrol',2]])->get();

        foreach($vendedores as $vendedor) {
            if ($vendedor['condicion'] == 1) {
                $user = User::findOrFail($vendedor['id']);
                $user->condicion = '0';
                $user->save();
            } else {
                $user = User::findOrFail($vendedor['id']);
                $user->condicion = '1';
                $user->save();
            }
        }
    }
}
