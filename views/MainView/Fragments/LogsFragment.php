<script>
    function next()
    {
        let maxSize = <?php echo $this->getFragmentArray()['maxSize']?? '0'?>;
        if(maxSize!=0&&!window.location.href.includes("?page",0))
        {
            window.location.href = window.location.href+"?page=1";
        }
        else if(maxSize!=0&&maxSize>window.location.href.split('?page=')[1])
        {
            window.location.href = window.location.href.split('?')[0] + "?page=" + (++(window.location.href.split('?page=')[1]));
        }
    }
    function previous()
    {
        if(!window.location.href.includes("?page",0))
        {
            window.location.href = window.location.href+"?page=0"+page;
        }
        else if((window.location.href.split('?page=')[1])!=0)
        {
            window.location.href = window.location.href.split('?')[0] + "?page=" + (--(window.location.href.split('?page=')[1]));
        }
    }
</script>
<div class="title">
    <h1><strong><?php echo $this->getFragmentArray()['title']?? ''; ?></strong></h1>
</div>
<div class="content center_parent">
    <div class="centered">
        <nav aria-label="Page navigation example">
            <ul class="pagination">
                <li class="page-item"><button class="btn" onclick="previous()">Previous</button></li>
                <li class="page-item"><button class="btn" onclick="next()">Next</a></li>
                <li class="page-item">Page: <?php echo $this->getFragmentArray()['page_start'].'-'.$this->getFragmentArray()['page_end']?></li>
            </ul>
        </nav>
        <table class="table">
            <thead>
                <th>Time</th>
                <th>User</th>
                <th>Action</th>
            </thead>
            <tbody>
                <?php 
                    if(isset($this->getFragmentArray()['logs']))
                    {
                        foreach($this->getFragmentArray()['logs'] as $log)
                        {
                            echo '<tr><td>'.$log->getTime().'</td><td>'.$log->getUsername().'</td><td>'.$log->getAction().'</td></tr>';
                        }
                    }
                ?>
                
            </tbody>
        </table>
    </div>
</div>