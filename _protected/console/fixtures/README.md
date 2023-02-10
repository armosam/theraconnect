Generating fixture data and loading to database for testing and development purposes.
It is possible first generate necessary data as PHP arrays and random avatar images (it will generate in the uploads/test folder)

There is main fixture Role running what will load all fixtures because all other fixtures dependant from this fixture...

So after database migrations running following command will load whole database:
 >php yii fixture/load Role
  
To generate data you can run command

> php yii fixture/generate "user,user_avatar,auth_assignment"
>
> php yii fixture/generate "user,user_avatar,auth_assignment" --count=50
>
> php yii fixture/generate-all

There are `migrate.sh` utility that could be executed to automate the process of migration and test data loading into the database.