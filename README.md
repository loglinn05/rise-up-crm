# A CRM system module

## How it works

### Adding the user

Currently there are no users in the database.

![No users in the database](./screenshots/one.png)

Let's add one!

![Adding a user](./screenshots/two.png)

After we're done, if there are no validation errors, we see this toast.

![Successfully added a user](./screenshots/three.png)

Let's see what (or who) we have.

![Users were successfully added to the database](./screenshots/four.png)

Nice! We have successfully added some users.

What if we provide invalid data? Let's see!

![We provided invalid data and now we see validation errors](./screenshots/five.png)

Let's try to provide someone else's email.

![We provided a taken email](./screenshots/six.png)

### Changing the password

Let's change the first user's password. To test this, we first need to put some garbage into the `password` field. Let's do this in phpMyAdmin.

![We have put some garbage instead of the user's password](./screenshots/seven.png)

Now, let's click that "Change Password" button. We'll see a popup.

!["Change Password" popup](./screenshots/eight.png)

Let's enter a new password and confirm it.

![We entered a new password](./screenshots/nine.png)

We have provided valid data, so we see this green notification.

![We changed the password](./screenshots/ten.png)

It really worked!

![The password was really changed](./screenshots/eleven.png)

Now let's try to provide invalid data, for example, a password that's too short or is not the same as its confirmation.

![We provided invalid password](./screenshots/twelve.png)

And we see validation errors, as expected.

![The password is invalid](./screenshots/thirteen.png)
