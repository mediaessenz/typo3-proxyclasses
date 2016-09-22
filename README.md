# typo3-proxyclasses

## Installation

Use extension manager or composer

## Usage

Add the Vendor\ExtensionNames which should be extend to the extension manager settings (separated by comma)

e.g. `In2code\Femanager\`

Add Domain/Model and/or Controller with **exact** the same name than the origin in your extension

### Controller example

To extend the edit controller of femanager add Classes/Controller/EditController.php to your own extension.

Extend the origin controller and add your own methods:
```php
<?php
namespace Vendor\ExtensionName\Controller;

use \In2code\In2feuserextended\Domain\Model\User;

class EditController extends \In2code\Femanager\Controller\EditController
{
	/**
	 * an additional action
	 *
	 * @param User $user
	 * @return void
	 */
	public function yourAdditionalAction(User $user) {
		// do something
		$this->userRepository->update($user);
		$this->redirect('edit');
	}

}
```

### Model example

```php
<?php
namespace Vendor\ExtensionName\Domain\Model;

class User extends \In2code\Femanager\Domain\Model\User {

	/**
	 * longitude
	 *
	 * @var string
	 */
	protected $longitude;

	/**
	 * latitude
	 *
	 * @var string
	 */
	protected $latitude;

	/**
	 * @param string $latitude
	 */
	public function setLatitude($latitude) {
		$this->latitude = $latitude;
	}

	/**
	 * @return string
	 */
	public function getLatitude() {
		return $this->latitude;
	}

	/**
	 * @param string $longitude
	 */
	public function setLongitude($longitude) {
		$this->longitude = $longitude;
	}

	/**
	 * @return string
	 */
	public function getLongitude() {
		return $this->longitude;
	}

}
```
## Knowledge

The proxyclasses will be generated into the `typo3temp/Cache/Code/extensionkey` folder.

## Limitations

It's currently **not** possible to overwrite or extend existing methods.

It's also **not** possible to extend two classes from different extension with the same class path after the Vendor\ExtensioName part.

## Credits

Thanks to Georg Ringer for the initial work in his news extension!

