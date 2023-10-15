@if ($game->status == 'starting')
<button type="submit" name="action" value="switch">Switch</button>
<button type="submit" name="action" value="ready">Ready</button>
@elseif ($game->status == 'in_progress')

<button type="submit" name="action" value="draw_pile">Take pile</button>
<button type="submit" name="action" value="play_card">Play card</button>
<button type="submit" name="action" value="send_update">send_update</button>
@endif

