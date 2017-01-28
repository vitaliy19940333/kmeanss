<?
class Controller_Nav extends Controller
{
	public static $tree;
	public static $Instance;
	
	public  static function  Instance()
	{
		if(self::$Instance == null)
			self::$Instance = new Controller_Nav();
		return self::$Instance;
	}
	
	public function __construct()
	{
		parent::__construct();
		$allLink = $this->m_mysql->Select("Select * from navigation");
		$this->view_cat($this->parentChild($allLink));
	}	

	private function parentChild($result)
	{
				$rows = array();
				foreach($result as $key => $value)
				{
					if(empty([$value['parent_id']])) {
						$rows[$value['parent_id']] = array();
					}
						$rows[$value['parent_id']][] = $value;
				}
				return $rows;
	}
	
	public function bread_crumbs($path)
	{
		return $result = $this->m_mysql->Select("SELECT * FROM navigation WHERE link='".$path[0]."' OR link='".$path[1]."'");
	}
			
	private function view_cat($arr,$parent_id = 0) {

		if(empty($arr[$parent_id])) {
			return;
		}
		$this->tree = $this->tree.'<ul>';
		for($i = 0; $i < count($arr[$parent_id]);$i++) {
			if($parent_id != 0)
				$this->tree = $this->tree."<li><a href=http://".$_SERVER['SERVER_NAME']."/".$arr[0][$parent_id-1]['link']."/".$arr[$parent_id][$i]['link'].">".$arr[$parent_id][$i]['lang_'.$_SESSION['lang']]."</a>";
			else
				$this->tree = $this->tree."<li><a href=http://".$_SERVER['SERVER_NAME']."/".$arr[0][$i]['link'].">".$arr[$parent_id][$i]['lang_'.$_SESSION['lang']]."</a>";
			$this->view_cat($arr,$arr[$parent_id][$i]['id']);
			$this->tree = $this->tree.'</li>';
		}
		$this->tree = $this->tree.'</ul>';
	}
}
?>