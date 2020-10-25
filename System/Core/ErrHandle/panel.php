<html>
	<head>
	<title>Untitled</title>
	<meta name="viewport" content="width=device-width,initial-scale=1.0" />
	<style>
	*{margin:0;font-family:consolas;}
	#header{
		padding-bottom:14px;
		min-height:100px;
		box-sizing:border-box;
		border:1px solid;
		background-color:#383534;
	}
	#header h2{
		padding-top:14px;
		margin:0 0 0 70px;
		color:#fff;
	}
	#header p{
		margin:10px 0 0 70px;
		font-size:14px;
		color:#ffabab
	}
	#content{
		margin:10px 70px 0 70px;
		box-sizing:border-box;
		height:408px;
	}
	#content #panel-content{
		padding:10px 0 10px 0;
		font-size:12px;
		word-wrap:break-word;
	}
	.err-php_e{
		color:#40b8ff;
	}
	#content #title{
		padding:6px 0 6px 6px;
		color:blue;
		box-shadow:0px 2px 6px #383534;
		word-wrap:break-word
	}
	@media screen and (max-width:1024px)
	{
		#content{
			margin:10px 40px 0 40px;
		}
	}
	@media screen and (max-width:850px)
	{
		#header h2{
			padding-top:8px;
			margin:0 0 0 5px;
			font-size:18px;
		}
		#header p{
			margin:10px 0 0 5px;
			font-size:12px;
		}
		#content{
			margin:10px 8px 0 8px;
		}
	}
	@media screen and (max-width:550px)
	{
		#content p{
			font-size:10px
		}
	}

	</style>
	</head>
	<body>
		<div id="header">
			<h2>Error Exception</h2>
			<p><?php echo $this->errors->msg ?></p>
		</div>
		<div id="content">
			<h5 id="title">Source: <span style="color:#383534">
			<?php
				if($this->errors->file != '')
				{
					$path = str_replace(str_replace("/","\\",$this->fakePath), '', $this->errors->file);
					
					echo $path.':'.$this->errors->line;
				}
			?>
			</span></h5>
			<div id="panel-content" style="line-height:12px;color:#fff;background-color:#383534">
			<?php
			include 'ErrorContent.php';
			
			$line_err = $this->errors->line;

			if($this->errors->line > 0)
			{
				if($this->errors->file != '')
				{
					foreach(file($this->errors->file) as $line => $content)
					{
						if(($line+1) >= $line_err - 10)
						{
							if($line_err == $line + 1)
							{
								$content = htmlspecialchars($content);
								
								$txt = "<font style='background-color:red'>{$content}</font>";
								$red = 'background-color:red';
							}
							else
							{
								$txt = \System\Core\ErrHandle\ErrorContent::analize($content);
								$red = '';
							}

							switch(strlen($line+1))
							{
								case 1: $space=4; break;
								case 2: $space=3; break;
								case 3: $space=2; break;
								case 4: $space=1; break;
							}
							$count = str_repeat('&nbsp;',$space);
							
							echo "<p style='{$red}'>
							<font>&nbsp;&nbsp;".($line+1)."$count</font><font color='#a2a2a2'>|</font>".str_replace(["\t","\n"],['&nbsp;&nbsp;&nbsp;&nbsp;',''], $txt).'<br></p>';
						}

						if(($line+1) == ($line_err+10)){
							break;
						}
					}
				}
			}
			?>
			</div>
			<div style="margin:18px 0 8px 0">
				Contact developer didaputraa@gmail.com
			</div>
		</div>
	</body>
</html>