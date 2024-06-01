<!DOCTYPE html>
<!---Coding By CodingLab | www.codinglabweb.com--->
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    <!---Custom CSS File--->
    <link rel="stylesheet" href="requestresched.css" />
  </head>
  <body>
    <section class="container">
      <header>Request Re-schedule</header>
      <form action="#" class="form">
        <div class="column">
          <div class="input-box">
            <label>Last Name</label>
            <input
              disabled="text"
              placeholder="Enter last name"
              required
              style="color: black; background: white; border-color: gray"
            />
          </div>

          <div class="input-box">
            <label>First Name</label>
            <input
              disabled="text"
              placeholder="Enter First name"
              required
              style="color: black; background: white; border-color: gray"
            />
          </div>
        </div>

        <div class="input-box">
          <label>Reason for re-scheduling</label>
          <textarea required> </textarea>
        </div>

        <div class="column">
          <button>Send Request</button>
        </div>
      </form>
    </section>
  </body>
</html>
