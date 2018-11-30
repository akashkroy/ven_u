<?php namespace OurScene\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use OurScene\Helpers\Chat;

class ChatServer extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'chat_server:serve';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Command description.';

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
	public function fire()
	{
		//
		$this->info("Chat Server Started");

		$server = IoServer::factory(
		    new HttpServer(
		        new WsServer(
		            new Chat()
		        )
		    ),
		    8080
		);

		$server->run();

	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [
			// ['example', InputArgument::REQUIRED, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [
			// ['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
