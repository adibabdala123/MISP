<div class='form'>
    <h2><?= __('Update forked cluster') ?></h2>
    <?php if(!$newVersionAvailable): ?>
        <div class="alert alert-warning"><?= __('No new version available') ?></div>
    <?php else: ?>
        <p><?= __('This interface allows you to update the selected cluster to the latest version of its parent.') ?></p>
        <p><?= __('You can pick galaxy cluster\'s elements to import from the parent to the selected cluster.') ?></p>

        <div class="row" style="max-height: 500px;">
            <div class="span6">
                <h4><?= __('Parent fork elements') ?></h4>
                <div class="alert alert-success" style="margin-bottom: 0px">
                    <div><?= sprintf(__('Version: %s'), h($parentVersion)) ?></div>
                </div>
                <table class="table table-striped table-hover table-condensed">
                    <thead>
                        <tr>
                            <th><?= __('Key'); ?></th>
                            <th><?= __('Value'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($parentElements)): ?>
                            <tr>
                                <td colspan="2"><?= __('No cluster element') ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($parentElements as $k => $element): ?>
                                <tr>
                                    <td><?= h($element['key']) ?></td>
                                    <td><?= h($element['value']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="span6">
                <h4><?= __('Current elements') ?></h4>
                <div class="alert alert-warning" style="margin-bottom: 0px">
                    <div><?= sprintf(__('Parent version: %s'), h($forkVersion)) ?></div>
                </div>
                <table class="table table-striped table-hover table-condensed">
                    <thead>
                        <tr>
                            <th><?= __('Key'); ?></th>
                            <th><?= __('Value'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($clusterElements)): ?>
                            <tr>
                                <td colspan="2"><?= __('No cluster element') ?></td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($clusterElements as $k => $element): ?>
                                <tr>
                                    <td><?= h($element['key']) ?></td>
                                    <td><?= h($element['value']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <h4><?= __('Elements in parent fork but not in this cluster') ?></h4>
        <?php echo $this->Form->create('GalaxyCluster');?>
        <div class="row">
            <div class="span8">
                <table class="table table-striped table-hover table-condensed">
                    <thead>
                        <tr>
                            <th><?= __('Key'); ?></th>
                            <th><?= __('Value'); ?></th>
                            <th><?= __('Import element into the cluster') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($missingElements as $k => $element): ?>
                            <tr>
                                <td><?= h($element['key']) ?></td>
                                <td><?= h($element['value']) ?></td>
                                <td>
                                    <?php
                                        echo $this->Form->input('element-'.$k, array(
                                            'label' => __('Import'),
                                            'type' => 'checkbox',
                                            'checked' => true
                                        ));
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php
            echo $this->Form->button(__('Update'), array('class' => 'btn btn-primary'));
            echo $this->Form->end();
        ?>
    <?php endif; ?>
</div>

<?php echo $this->element('/genericElements/SideMenu/side_menu', array('menuList' => 'galaxies', 'menuItem' => 'update_cluster')); ?>
</div>