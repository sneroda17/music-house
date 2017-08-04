<?php

class PagesTableSeeder extends Seeder {

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{
		\DB::table('pages')->truncate();
        
		\DB::table('pages')->insert(array (
			0 => 
			array (
				'id' => 1,
				'title' => 'Terms',
				'description' => '<b>Accepting the Terms of Service</b>
<ul>
<li>lorem ipsum 1</li>
<li>lorem ipsum 2</li>
<li>lorem ipsum 3</li>
<li>lorem ipsum 4</li>
</ul>',
			),
			1 => 
			array (
				'id' => 2,
				'title' => 'Privacy',
				'description' => '<b>What is copyright?</b>
<b>What is copyright infringement?</b>
<p>Copyright infringement occurs when a copyrighted work is reproduced, distributed, performed, publicly displayed, or made into a derivative work without the permission of the copyright owner.</p>
<p>
As a general matter, we at BinaryZeal respect the rights of artists and creators, and hope you will work with us to keep our community a creative, legal and positive experience for everyone, including artists and creators.
</p>
<p>
Please note that under Section 512(f) any person who knowingly materially misrepresents that material or activity is infringing may be subject to liability for damages. Don\'t make false claims!
</p>
<p>
Please also note that the information provided in this legal notice may be forwarded to the person who provided the allegedly infringing content. 
</p>',
			),
			2 => 
			array (
				'id' => 3,
				'title' => 'FAQ',
				'description' => '<ul>
<li>lorem ipsum 1</li>
<li>lorem ipsum 2</li>
<li>lorem ipsum 3</li>
<li>lorem ipsum 4</li>
</ul>',
			),
			3 => 
			array (
				'id' => 4,
				'title' => 'contact',
				'description' => 'This is a contact page',
			),
		));
	}

}
