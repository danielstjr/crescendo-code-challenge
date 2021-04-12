<p>
Coding Challenge for Crescendo Collective. 
Bulk of code is the Laravel fresh install package from running "composer create-project laravel/laravel crescendo-code-challenge" 
</p>

<p>
Api endpoint served as a GET request at /api/yelp, and defaults to getting reviews from Jack Stack BBQ. 
It supports the following parameters to grab the first business returned from Yelp v3 API in this order of
preference.
</p>

<ol>
<li>id</li>
<li>latitude/longitude, if one is passed in the other is required</li>
<li>location</li>
</ol>

<p>Files to checkout:</p>
<ul>
<li>routes/api.php</li>
<li>routes/web.php</li>
<li>app/Http/Controllers/YelpController.php</li>
<li>app/Providers/YelpServiceProvider.php</li>
<li>app/Yelp/Business.php</li>
<li>app/Yelp/Client.php</li>
</ul>
