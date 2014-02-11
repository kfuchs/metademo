# Reposed
### The toolkit that works with eloquent

## Contains
1. An extended Model with quite a bit of functionality over eloquent.
2. A repository implementation that uses functional programming (most probably monads) to provide repositories that compliment eloquent.
3. A persistor, which basically allows you to define an object with rules for handling model persistence. You can even hook in closures for special cases.
4. A filterer, which allows you to define filtering rules for resource index as key => value pairs. You can customize it again using closures.
5. An orderer, which again just allows you to define ordering rules using key => value pairs.
6. And a serializer, its slower than laravel's own serializer, but much more powerful.

## So, the docs

-

### Models

There were several things I found lacking in eloquent, that I have added onto my reposed model.

-

### Repositories

They rock. Seriously.

There are just a few methods that you need to know about at the moment. Rest all is basically query builder. At least for now
The repositories work in a way that they can be made query independent. Which essentially makes them db independent.

**One big advantage Reposed gives you is chainability.** When you use the default Scoping functions on Eloquent models, you are returned a Query object. But the Reposed functions (the two main `newScoped()` and `newJoint()`) return not even the Eloquent object, but the *Repository* object, which is even more powerful (and customized in your DSL) than just Eloquent.

So, here are the main helpful methods:

#### newScoped(Closure $scope)

Usage:

```
class PostsRepository extends k\Reposed\Repository {
	
	public function forUser(User $user)
	{
		return $this->newScoped(function ($q) use ($user) {
			$q->where('user_id', '=', $user->id);
		});
	}

}
```
there is also a pretty cool helper for column names and stuff

```
class PostsRepository extends k\Reposed\Repository {
	
	public function forUser(User $user)
	{
		return $this->newScoped(function ($q) use ($user) {
			$q->where($this->c('user_id', '=', $user->id));
		});
	}
	
}
```

#### newJoint(Closure $scope)

A couple advantages to using Joints to handle your JOIN queries:

1. It applies one type of JOIN only once, no matter how many times you call it from different functions.

2. It returns Reposed repositories.

Usage:

```
class PostsRepository extends k\Reposed\Repository {
	
	public function joinPostPictures()
	{
		return $this->newJoint(function ($q) {
			$q->join(
				PhotosModel::table(),
				PhotosModel::c('post_id'), '=', $this->c('id')
			);
		});
	}
}
```
