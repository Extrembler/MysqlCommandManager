<?php
/**
 * @package Extrembler 
 * @category Extrembler_MysqlCommandManager
 * @author Extrembler
*/

namespace Extrembler\MysqlCommandManager\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Helper\Table;

class MysqlRunDeveloper extends Command
{
	/**
	 * constance for command parameter
	*/
	const QUERY_PARAM = 'query';

	/**
	 * update query string 
	*/
	const UPDATE_STRING = 'update';

	/**
	 * select query string 
	*/
	const SELECT_STRING = 'select';

	/**
	 * show query string 
	*/
	const SHOW_STRING = 'show';

	/**
	 * @var \Magento\Framework\App\ResourceConnection 
	*/
	protected $_resourceConnection;
	
	/**
	 * @var array 
	*/
	protected $_allowedMethods = [];

	/**
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
	public function __construct(
		\Magento\Framework\App\ResourceConnection $resourceConnection
	)
	{
		$this->_allowedMethods = [self::SELECT_STRING,self::UPDATE_STRING,self::SHOW_STRING];
		$this->_resourceConnection = $resourceConnection;
		parent::__construct();
	}

	/**
	 * @param void
	 * @return parent::configure()
	*/
	protected function configure()
   	{
       $options = [
			new InputOption(
				self::QUERY_PARAM,
				null,
				InputOption::VALUE_REQUIRED,
				'Query'
			)
		];

       	$this->setName('sql:runQueryDeveloperOnly');
       	$this->setDescription('Run Sql Query through command line, we suggest to use where and required columns only for better result, caution: IF YOU ARE NOT EXPERT DO NOT USE THIS');
		$this->setDefinition($options);
       
       	parent::configure();
   	}

   	/**
   	 * @param InputInterface $input
   	 * @param OutputInterface $output
   	 * @return $this 
   	*/
   	protected function execute(InputInterface $input, OutputInterface $output)
   	{
		if ($query = trim($input->getOption(self::QUERY_PARAM))) {
   			
   			$table = new Table($output);
   			$queryFirstString = '';
   			if($explodeString = explode(' ',$query)){
   				if(isset($explodeString[0])){
   					$queryFirstString = strtolower($explodeString[0]);
   					if(!in_array($queryFirstString, $this->_allowedMethods)){
   						$table->setHeaders([sprintf('We do not support %s query, we are sorry for this but you can contribute as we are making as open source project',$queryFirstString)]);
	   					$table->render();
	   					return $this;
   					}
   				}
   			}

   			$result = $this->_resourceConnection->getConnection()->query($query);

   			if($queryFirstString == self::UPDATE_STRING){
   				// do not need to do fetch all
   				$table->setHeaders([sprintf("Total %s row(s) updated successfully",$result->rowCount())]);
   			}
   			else{
   				$result = $result->fetchAll();
   				$table->setHeaders(array_keys($result[0]));
	   			if(!empty($result)){
		        	$table->setRows($result);
	   			}
	   			else{
	   				$table->setHeaders(['No Record Found']);
	   			}
   			}
	     	$table->render();
		}
		else{
			$output->writeln("Please specify query parameter");
		}

		return $this;
   	}
}
