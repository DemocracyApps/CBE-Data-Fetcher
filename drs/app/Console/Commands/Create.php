<?php namespace App\Console\Commands;

use App\DRSObjects\FetchTask;
use Illuminate\Console\Command;

class Create extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'drs:create {type} {data}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Create a [job, ...]';

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

    $type = $this->argument('type');
    $data = $this->argument('data');

    if ($type == 'job') { // We expect a job file
      echo "Importing job definition from " . $data . PHP_EOL;
      $str = file_get_contents($data);
      $spec = json_decode($str, true);
      if ( ! $spec) {
        throw new \Exception("Error parsing job file " . $data);
      }

      $ft = new FetchTask();
      $ft->initializeFromSpec($spec);
      $ft->save();
      echo "New fetch task created - ID = $ft->id" . PHP_EOL;
      echo "Scheduled for next fetch at " . $ft->next . PHP_EOL;
    }
  }
}
