      <?php
                                $i = 1;
                                $pagination_tr = '';
                                if (count($this->paginator) != 0) {
                                    foreach ($this->paginator as $results) {
                                        ?>
                                        <tr>
                                             <td>
                                                 <input type="checkbox" name="selected_id[]" id="selected_id<?=$i; ?>" />
                                            </td>
                                            <td><?php echo $i;

                                $i++;
                                ?>
                                            </td>						
                                            <td><?php echo $results['course_id']['course_code']; ?></td>

                                            <td><?php echo $results['document_title']; ?></td>

                                            <td><a href='<?= $this->baseUrl($results['filename']); ?>' class='link' download><?php echo $results['filename1']; ?></a></td>
                                            <td><?php echo $results['remarks']; ?></td>
                                            <td><?= $results['updated_date']; ?></td>
                                            <td><?= $results['due_date']; ?></td>
                                        </tr>            
    <?php
    }
}
?>
