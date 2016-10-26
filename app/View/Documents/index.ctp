<div id="document-list">
    <?php
    if (is_null($currentFolder)) {
        $currentFolderName = "root";
    ?>

    <h3>Folders</h3>
    <table>
        <?php foreach ($folders as $folder): ?>
        <tr>
            <td><?php echo $this->Html->image('folder.png', array(
                                      'alt' => 'folder',
                                      'title' => 'go to folder',
                                      'url' => array('action'=>'index', $folder['Catalogue']['id'])
                                  )
                       );
                 ?></td>
            <td><a href="/tagging/documents/index/<?php echo $folder['Catalogue']['id'];?>"><?php echo $folder['Catalogue']['name']; ?></a></td>
            <td>
                <?php
                    echo $this->Html->link(
                        'Edit',
                        array('controller' => 'catalogues', 'action' => 'edit', $folder['Catalogue']['id'])
                    );
                ?>
                &nbsp;&nbsp;
                <?php
                    echo $this->Form->postLink(
                        'Delete',
                        array('controller' => 'catalogues', 'action' => 'delete', $folder['Catalogue']['id']),
                        array('confirm' => 'Documents from this folder will be moved to the root folder. Are you sure?')
                    );
                ?>
            </td>
        </tr>
        <?php endforeach; ?>
	</table>
	<a style="cursor:pointer;" onclick="folderAddForm()">+ Add folder</a>
	<div id="folder_add_form"class="hidden">
    <?php
    echo $this->Form->create('Catalogue', array('action' => 'add'));
    echo $this->Form->input('name', array('label' => 'New folder name:'));
    echo $this->Form->end('Add');
    ?>
    </div>
    <br/><br/>
    <?php
    } else {
        echo $this->Html->image('left.png', array(
                                          'alt' => 'back to the root folder',
                                          'title' => 'back to the root folder',
                                          'url' => array('action'=>'index')
                                      )
                           );
        $currentFolderName = $currentFolder['Catalogue']['name'];
    }
    ?>
    <h3>Documents in folder: <?php echo $currentFolderName; ?></h3>
    <table>
        <tr>
            <th>Id</th>
            <th></th>
            <th>Name</th>
            <th>Language</th>
            <th>Owner</th>
            <th>Words</th>
            <th width="200px">Actions</th>
        </tr>

        <?php foreach ($documents as $document): ?>
        <tr>
            <td><?php echo $document['Document']['id']; ?></td>
            <td><?php echo $this->Html->image("edit.png", array(
                                    "alt" => "edit",
                                    'url' => array('controller' => 'dashboard', 'action' => 'setCurrentDocument', $document['Document']['id'], 0)
                                     ));
                ?>
            </td>
            <td>
                <?php
                    echo $this->Html->link(
                        $document['Document']['name'],
                        array('action' => 'view', $document['Document']['id'])
                    );
                ?>
            </td>
            <td><?php echo $document['Language']['description']; ?> (<?php echo $document['Language']['code']; ?>)</td>
            <td><?php echo $document['User']['username']; ?></td>
            <td><?php echo $wordCounts[$document['Document']['id']]; ?></td>
            <td>
                <?php if ($roleId < 3) { ?>

                <?php
                    echo $this->Html->link(
                        'Edit',
                        array('action' => 'edit', $document['Document']['id'])
                    );
                ?>
                &nbsp;&nbsp;
                <?php
                    echo $this->Form->postLink(
                        'Delete',
                        array('action' => 'delete', $document['Document']['id']),
                        array('confirm' => 'Deleting a document will also delete all the annotations. Are you sure?')
                    );
                ?>
                &nbsp;&nbsp;
                <?php echo $this->Html->image("excel.png", array(
                                    "alt" => "export to Excel",
                                    "title" => "export document to Excel",
                                    "width" => "24px",
                                    'url' => array('controller' => 'generator', 'action' => 'generatedocxlsx', $document['Document']['id'])
                                     ));
                ?>
                <select id="folderSelect<?php echo $document['Document']['id'];?>" onchange="moveToFolder(this, <?php echo $document['Document']['id'];?>)" style="width:100px">
                    <option value="-1">Move to...</option>
                    <option value="0">root</option>
                    <?php foreach ($folders as $folder): ?>
                    <option value="<?php echo $folder['Catalogue']['id'];?>"><?php echo $folder['Catalogue']['name'];?></option>
                    <?php endforeach; ?>
                </select>
                <?php } ?>
            </td>
        </tr>
        <?php endforeach; ?>

    </table>
</div>

<div id="import-box">
    <h3>Import document from a text file</h3>
    <?php
    echo $this->Form->create('Documents', array('type' => 'file', 'action' => 'add'));
    echo $this->Form->input('file',array( 'type' => 'file'));
    echo $this->Form->input('language', array('type' => 'select', 'options' => $languageOptions,'empty' => false));
    echo $this->Form->end('Submit');
    ?>
</div>
