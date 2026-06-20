<?php

declare(strict_types=1);

namespace App\Modules\Trips\Controllers;

use App\Controllers\BaseController;
use App\Modules\Trips\Models\SystemSettingModel;
use App\Modules\Trips\Libraries\TripQueryService;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\I18n\Time;

/**
 * SystemSettingsController
 *
 * Handles system-wide settings management (base booking fee, etc).
 *
 * @package App\Modules\Trips\Controllers
 * @author Senior Developer
 * @since 1.0.0
 */
class SystemSettingsController extends BaseController
{
    private SystemSettingModel $settingModel;
    private TripQueryService $queryService;

    public function __construct()
    {
        $this->settingModel = new SystemSettingModel();
        $this->queryService = service('tripQueryService');
    }

    /**
     * Update system settings (base booking fee, etc).
     */
    public function updateSystemSettings(): ResponseInterface
    {
        $rules = [
            'base_booking_fee' => 'required|numeric|greater_than_equal_to[0]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $fee = (float) $this->request->getPost('base_booking_fee');
        $setting = $this->queryService->getSystemSetting('base_booking_fee');

        if ($setting !== null) {
            $this->settingModel->update($setting['id'], [
                'setting_value' => (string) number_format($fee, 2, '.', ''),
                'updated_by'    => session()->get('userId'),
                'updated_at'    => Time::now()->toDateTimeString(),
            ]);
        } else {
            $this->settingModel->insert([
                'setting_key'   => 'base_booking_fee',
                'setting_value' => (string) number_format($fee, 2, '.', ''),
                'updated_by'    => session()->get('userId'),
                'updated_at'    => Time::now()->toDateTimeString(),
            ]);
        }

        return redirect()->to(url_to('trips.manager'))
            ->with('success', 'Base booking fee updated to $' . number_format($fee, 2) . '.');
    }
}