<?php

namespace App\Console\Commands;

use App\Models\Factory\WhaleTickFactory;
use App\Models\Orm\UserAuth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DataUserMatchCommand extends AppCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'DataUserMatchCommand';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '匹配迁移';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        Log::info('开始程序!', ['code' => 5555]);
        try
        {
            //针对sd_data_user_match表中是否有数据,如果有根据user_id进行判断处理
            $matchs = WhaleTickFactory::getDataUserMatchLast();
            if(empty($matchs))
            {
                $userId = 0;
            } else {
                $userId = $matchs->user_id;
            }

            $query = UserAuth::when($userId, function($query) use($userId){
                return $query->where('sd_user_id', '>', $userId);
            });
            $query->select(['sd_user_id','mobile','create_at'])->chunk(1000,function($messages) {
                foreach ($messages as $message)
                {
                    $data['md5Mobile'] = md5($message['mobile']);
                    $data['dmd5Mobile'] = md5(md5($message['mobile']));
                    $data['registered_at'] = $message['create_at'];
                    $data['mobile'] = $message['mobile'];
                    $data['user_id'] = $message['sd_user_id'];

                    WhaleTickFactory::insertMobileDB($data);
                }

                Log::info('user_match',['messgae' => 1000, 'code' => 10001]);
            });
        }
        catch (\Exception $ex)
        {
            \Log::error($ex);
        }
    }














}
