<?php
/**
 * My Account → Reading Journey.
 *
 * @package Alphonsus
 */

defined( 'ABSPATH' ) || exit;

$alph_data   = alph_rj_get();
$alph_today  = alph_rj_today();
$alph_min    = alph_rj_minutes_on( $alph_data, $alph_today );
$alph_goal   = (int) $alph_data['goal'];
$alph_days   = alph_rj_days( $alph_data, 7 );
$alph_prev   = array_sum( array_slice( alph_rj_days( $alph_data, 14 ), 0, 7 ) );
$alph_week   = array_sum( $alph_days );
$alph_streak = alph_rj_streak( $alph_data );
$alph_xp     = alph_rj_xp( $alph_data );
$alph_level  = alph_rj_level( $alph_xp );
$alph_books  = alph_rj_books( $alph_data );
$alph_mix    = alph_rj_mix( $alph_data, 7 );
$alph_done   = $alph_data['quests'][ $alph_today ] ?? array();
$alph_is_wkd = in_array( (int) wp_date( 'N' ), array( 6, 7 ), true );
$alph_left   = max( 0, $alph_goal - $alph_min );
$alph_open   = array_values( array_filter( $alph_books, fn( $b ) => ! $b['finished'] ) );
$alph_csv    = wp_nonce_url( admin_url( 'admin-post.php?action=alph_rj_csv' ), 'alph_rj_csv' );
?>
<div class="rj" data-rj>
	<div class="rj__head">
		<div>
			<h2 class="dash-title"><?php esc_html_e( 'Reading Journey & Daily Habits', 'alphonsus' ); ?></h2>
			<p class="muted"><?php esc_html_e( 'Track your reading minutes, streaks and goals across storybooks and Swedish studies.', 'alphonsus' ); ?></p>
		</div>
		<?php /* translators: 1: start date, 2: end date */ ?>
		<span class="chip chip--paper"><?php echo alph_icon( 'calendar', 13 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><?php echo esc_html( sprintf( __( 'This week: %1$s – %2$s', 'alphonsus' ), wp_date( 'M j', strtotime( array_key_first( $alph_days ) ) ), wp_date( 'M j', strtotime( $alph_today ) ) ) ); ?></span>
	</div>

	<ul class="rj-stats">
		<li><span class="rj-stats__icon rj-stats__icon--amber"><?php echo alph_icon( 'flame', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span><small><?php esc_html_e( 'Current streak', 'alphonsus' ); ?></small><?php /* translators: %d: days */ ?><strong><?php echo esc_html( sprintf( _n( '%d-day streak', '%d-day streak', $alph_streak, 'alphonsus' ), $alph_streak ) ); ?></strong></span></li>
		<li><span class="rj-stats__icon"><?php echo alph_icon( 'book-open', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span><small><?php esc_html_e( 'Weekly total', 'alphonsus' ); ?></small><?php /* translators: %d: minutes */ ?><strong><?php echo esc_html( sprintf( __( '%d mins', 'alphonsus' ), $alph_week ) ); ?></strong></span></li>
		<li><span class="rj-stats__icon"><?php echo alph_icon( 'zap', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span><small><?php esc_html_e( 'Polyglot XP', 'alphonsus' ); ?></small><strong><?php echo esc_html( number_format_i18n( $alph_xp ) ); ?> XP</strong></span></li>
		<li><span class="rj-stats__icon rj-stats__icon--green"><?php echo alph_icon( 'award', 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span><span><small><?php esc_html_e( 'Reader level', 'alphonsus' ); ?></small><strong class="green"><?php echo esc_html( $alph_level[0] . ( $alph_level[2] ? ' (' . $alph_level[3] . '%)' : '' ) ); ?></strong></span></li>
	</ul>

	<section class="card rj-today" id="timer">
		<div class="rj-today__ring">
			<div class="ring">
				<?php echo alph_ring_svg( $alph_min, $alph_goal ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div class="ring__center">
					<small><?php esc_html_e( 'Today’s target', 'alphonsus' ); ?></small>
					<strong data-rj-today><?php echo esc_html( $alph_min ); ?></strong><span>/ <?php echo esc_html( $alph_goal ); ?> min</span>
					<?php /* translators: %d: percent */ ?>
					<em><?php echo esc_html( sprintf( __( '%d%% completed', 'alphonsus' ), $alph_goal ? min( 100, round( $alph_min / $alph_goal * 100 ) ) : 0 ) ); ?></em>
				</div>
			</div>
			<p class="rj-today__note">
				<?php echo alph_icon( 'clock', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<?php
				/* translators: %d: minutes */
				echo esc_html( $alph_left ? sprintf( __( '%d mins to goal', 'alphonsus' ), $alph_left ) : __( 'Goal reached!', 'alphonsus' ) );
				?>
			</p>
		</div>
		<div class="rj-today__copy">
			<span class="chip chip--green"><span class="dot" aria-hidden="true"></span><?php esc_html_e( 'Today’s session', 'alphonsus' ); ?></span>
			<h3 class="rj-today__title">
				<?php
				if ( $alph_open ) {
					/* translators: %s: book title */
					echo esc_html( sprintf( __( 'Continue reading “%s”', 'alphonsus' ), $alph_open[0]['product']->get_name() ) );
				} else {
					esc_html_e( 'Pick a book and start a reading session', 'alphonsus' );
				}
				?>
			</h3>
			<p class="muted">
				<?php
				if ( $alph_left && $alph_streak ) {
					/* translators: 1: minutes left, 2: streak */
					echo esc_html( sprintf( __( 'Read %1$d more minutes today to keep your %2$d-day streak growing.', 'alphonsus' ), $alph_left, $alph_streak ) );
				} elseif ( $alph_left ) {
					/* translators: %d: minutes */
					echo esc_html( sprintf( __( 'Read %d minutes today to start a new streak.', 'alphonsus' ), $alph_left ) );
				} else {
					esc_html_e( 'You’ve hit today’s goal. Every extra minute still earns XP.', 'alphonsus' );
				}
				?>
			</p>

			<div class="timer" data-rj-timer>
				<label class="screen-reader-text" for="rj-book"><?php esc_html_e( 'Book you are reading', 'alphonsus' ); ?></label>
				<span class="select select--sm">
					<select id="rj-book" data-rj-book>
						<option value="0"><?php esc_html_e( 'Any book', 'alphonsus' ); ?></option>
						<?php foreach ( $alph_books as $alph_b ) : ?>
							<option value="<?php echo esc_attr( $alph_b['product']->get_id() ); ?>"><?php echo esc_html( $alph_b['product']->get_name() ); ?></option>
						<?php endforeach; ?>
					</select>
					<?php echo alph_icon( 'chevron', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				</span>
				<span class="timer__clock" data-rj-clock aria-live="polite">00:00</span>
				<button type="button" class="btn btn--primary" data-rj-start><?php echo alph_icon( 'play', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Start reading timer', 'alphonsus' ); ?></span></button>
				<button type="button" class="btn btn--ghost" data-rj-pause hidden><?php echo alph_icon( 'pause', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Pause', 'alphonsus' ); ?></span></button>
				<button type="button" class="btn btn--ghost" data-rj-log-toggle aria-expanded="false" aria-controls="rj-log"><?php echo alph_icon( 'list-plus', 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?><span><?php esc_html_e( 'Log offline reading', 'alphonsus' ); ?></span></button>
			</div>

			<form class="rj-log" id="rj-log" data-rj-log hidden>
				<p class="field">
					<label for="rj-min"><?php esc_html_e( 'Minutes', 'alphonsus' ); ?></label>
					<input id="rj-min" name="minutes" type="number" min="1" max="600" value="20" required>
				</p>
				<p class="field">
					<label for="rj-date"><?php esc_html_e( 'When', 'alphonsus' ); ?></label>
					<span class="select"><select id="rj-date" name="date">
						<?php for ( $alph_i = 0; $alph_i < 7; $alph_i++ ) : ?>
							<?php $alph_d = wp_date( 'Y-m-d', strtotime( "-{$alph_i} days" ) ); ?>
							<option value="<?php echo esc_attr( $alph_d ); ?>"><?php echo esc_html( 0 === $alph_i ? __( 'Today', 'alphonsus' ) : ( 1 === $alph_i ? __( 'Yesterday', 'alphonsus' ) : wp_date( 'l, M j', strtotime( $alph_d ) ) ) ); ?></option>
						<?php endfor; ?>
					</select><?php echo alph_icon( 'chevron', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				</p>
				<p class="field">
					<label for="rj-log-book"><?php esc_html_e( 'Book', 'alphonsus' ); ?></label>
					<span class="select"><select id="rj-log-book" name="product">
						<option value="0"><?php esc_html_e( 'Any book', 'alphonsus' ); ?></option>
						<?php foreach ( $alph_books as $alph_b ) : ?>
							<option value="<?php echo esc_attr( $alph_b['product']->get_id() ); ?>"><?php echo esc_html( $alph_b['product']->get_name() ); ?></option>
						<?php endforeach; ?>
					</select><?php echo alph_icon( 'chevron', 14 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
				</p>
				<button type="submit" class="btn btn--primary btn--sm"><?php esc_html_e( 'Save', 'alphonsus' ); ?></button>
			</form>

			<?php if ( $alph_data['log'] ) : ?>
				<p class="rj-today__fine"><button type="button" class="linkish" data-rj-undo><?php esc_html_e( 'Undo last entry', 'alphonsus' ); ?></button></p>
			<?php endif; ?>
		</div>
	</section>

	<div class="rj-grid">
		<div class="rj-col">
			<section class="card rj-card">
				<div class="rj-card__head">
					<div><h3><?php esc_html_e( 'Weekly Reading Velocity', 'alphonsus' ); ?></h3><p class="muted"><?php esc_html_e( 'Daily minutes across textbooks and storybooks', 'alphonsus' ); ?></p></div>
					<?php /* translators: %d: minutes */ ?>
					<ul class="chart-legend"><li><span class="swatch swatch--navy"></span><?php esc_html_e( 'Read time', 'alphonsus' ); ?></li><li><span class="swatch swatch--line"></span><?php echo esc_html( sprintf( __( '%d-min target', 'alphonsus' ), $alph_goal ) ); ?></li></ul>
				</div>
				<?php echo alph_week_chart_svg( $alph_days, $alph_goal ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
				<div class="rj-card__foot">
					<span>
						<?php echo alph_icon( 'trending', 16 ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
						<?php
						if ( $alph_prev > 0 ) {
							$alph_change = round( ( $alph_week - $alph_prev ) / $alph_prev * 100 );
							/* translators: %d: percent */
							echo esc_html( $alph_change >= 0 ? sprintf( __( '%d%% more reading than last week', 'alphonsus' ), $alph_change ) : sprintf( __( '%d%% less reading than last week', 'alphonsus' ), abs( $alph_change ) ) );
						} else {
							esc_html_e( 'Your first full week — keep going!', 'alphonsus' );
						}
						?>
					</span>
					<?php if ( $alph_data['log'] ) : ?>
						<a class="link-arrow" href="<?php echo esc_url( $alph_csv ); ?>"><?php esc_html_e( 'Download CSV', 'alphonsus' ); ?></a>
					<?php endif; ?>
				</div>
			</section>

			<section class="card rj-card">
				<div class="rj-card__head">
					<div><h3><?php esc_html_e( 'Books in Progress', 'alphonsus' ); ?></h3><p class="muted"><?php esc_html_e( 'Books you own or have logged. Slide to update how far you’ve read.', 'alphonsus' ); ?></p></div>
					<a class="link-arrow" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'Browse library', 'alphonsus' ); ?><?php echo alph_icon( 'arrow', 15 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
				</div>
				<?php if ( ! $alph_books ) : ?>
					<p class="empty-inline"><?php esc_html_e( 'Books you buy will appear here automatically. You can also log reading for any book from the timer above.', 'alphonsus' ); ?></p>
				<?php endif; ?>
				<ul class="books-progress">
					<?php foreach ( array_slice( $alph_books, 0, 6 ) as $alph_b ) : ?>
						<?php $alph_p = $alph_b['product']; ?>
						<li class="bp<?php echo $alph_b['finished'] ? ' bp--done' : ''; ?>">
							<a class="bp__thumb" href="<?php echo esc_url( $alph_p->get_permalink() ); ?>" tabindex="-1" aria-hidden="true"><?php echo $alph_p->get_image( 'thumbnail' ); // phpcs:ignore WordPress.Security.EscapeOutput ?></a>
							<div class="bp__info">
								<p class="bp__meta">
									<span class="chip chip--<?php echo $alph_b['finished'] ? 'green' : 'navy'; ?> chip--xs"><?php echo esc_html( $alph_b['finished'] ? __( 'Completed', 'alphonsus' ) : alph_rj_subject( $alph_p->get_id() ) ); ?></span>
									<?php /* translators: %s: hours */ ?>
									<?php if ( $alph_b['minutes'] ) : ?><span><?php echo esc_html( sprintf( __( '%s hrs spent', 'alphonsus' ), number_format_i18n( $alph_b['minutes'] / 60, 1 ) ) ); ?></span><?php endif; ?>
								</p>
								<h4><?php echo esc_html( $alph_p->get_name() ); ?></h4>
								<p class="muted"><?php echo esc_html( alph_product_author( $alph_p ) ); ?></p>
							</div>
							<label class="bp__progress">
								<span class="bp__label"><?php esc_html_e( 'Progress', 'alphonsus' ); ?> <strong data-rj-pct><?php echo esc_html( $alph_b['percent'] ); ?>%</strong></span>
								<input type="range" min="0" max="100" step="5" value="<?php echo esc_attr( $alph_b['percent'] ); ?>" data-rj-progress="<?php echo esc_attr( $alph_p->get_id() ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: book */ __( 'Percent read of %s', 'alphonsus' ), $alph_p->get_name() ) ); ?>" style="--pct: <?php echo esc_attr( $alph_b['percent'] ); ?>%">
							</label>
						</li>
					<?php endforeach; ?>
				</ul>
			</section>
		</div>

		<div class="rj-col">
			<section class="card rj-card">
				<div class="rj-card__head">
					<div><h3><?php esc_html_e( 'Habits & Daily Quests', 'alphonsus' ); ?></h3><p class="muted"><?php esc_html_e( 'Tick off micro-habits for +25 XP each. They reset every day.', 'alphonsus' ); ?></p></div>
					<?php /* translators: %d: count */ ?>
					<span class="chip chip--lilac"><?php echo esc_html( sprintf( __( '%d done', 'alphonsus' ), count( $alph_done ) ) ); ?></span>
				</div>
				<ul class="quests">
					<?php foreach ( alph_rj_quests() as $alph_key => $alph_q ) : ?>
						<?php $alph_off = 'marathon' === $alph_key && ! $alph_is_wkd; ?>
						<li class="quest<?php echo $alph_off ? ' quest--off' : ''; ?>">
							<span class="quest__icon"><?php echo alph_icon( $alph_q[2], 18 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
							<span class="quest__text"><strong><?php echo esc_html( $alph_q[0] ); ?><?php if ( $alph_off ) : ?> <em><?php esc_html_e( 'Weekends', 'alphonsus' ); ?></em><?php else : ?> <em>+25 XP</em><?php endif; ?></strong><small><?php echo esc_html( $alph_q[1] ); ?></small></span>
							<label class="switch"><input type="checkbox" data-rj-quest="<?php echo esc_attr( $alph_key ); ?>" <?php checked( in_array( $alph_key, $alph_done, true ) ); ?> <?php disabled( $alph_off ); ?>><span class="switch__track" aria-hidden="true"></span><span class="screen-reader-text"><?php echo esc_html( $alph_q[0] ); ?></span></label>
						</li>
					<?php endforeach; ?>
				</ul>
				<p class="tier-title"><?php esc_html_e( 'Daily reading goal', 'alphonsus' ); ?></p>
				<div class="tiers-mini" role="radiogroup" aria-label="<?php esc_attr_e( 'Daily reading goal', 'alphonsus' ); ?>">
					<?php foreach ( alph_rj_tiers() as $alph_mins => $alph_label ) : ?>
						<button type="button" role="radio" aria-checked="<?php echo $alph_mins === $alph_goal ? 'true' : 'false'; ?>" class="tier-mini<?php echo $alph_mins === $alph_goal ? ' is-active' : ''; ?>" data-rj-goal="<?php echo esc_attr( $alph_mins ); ?>"><strong><?php echo esc_html( $alph_label ); ?></strong><small><?php echo esc_html( $alph_mins ); ?> min / day</small></button>
					<?php endforeach; ?>
				</div>
			</section>

			<section class="card rj-card">
				<div class="rj-card__head"><div><h3><?php esc_html_e( 'Literacy Subject Mix', 'alphonsus' ); ?></h3><p class="muted"><?php esc_html_e( 'Where your reading minutes went this week', 'alphonsus' ); ?></p></div></div>
				<?php if ( $alph_mix ) : ?>
					<div class="mix"><?php echo alph_donut_svg( $alph_mix, $alph_week . 'm', __( 'Total', 'alphonsus' ) ); // phpcs:ignore WordPress.Security.EscapeOutput ?></div>
				<?php else : ?>
					<p class="empty-inline"><?php esc_html_e( 'Log a session with a book selected to see your subject mix.', 'alphonsus' ); ?></p>
				<?php endif; ?>
			</section>
		</div>
	</div>

	<section class="card rj-card rj-badges">
		<div class="rj-card__head">
			<div><p class="eyebrow eyebrow--amber"><?php esc_html_e( 'Hall of honours', 'alphonsus' ); ?></p><h3><?php esc_html_e( 'Milestones & Achievements', 'alphonsus' ); ?></h3></div>
			<?php
			$alph_ach    = alph_rj_achievements( $alph_data );
			$alph_earned = count( array_filter( $alph_ach, fn( $a ) => $a[3] ) );
			?>
			<?php /* translators: 1: earned, 2: total */ ?>
			<span class="muted"><?php echo esc_html( sprintf( __( 'Badges earned: %1$d / %2$d', 'alphonsus' ), $alph_earned, count( $alph_ach ) ) ); ?></span>
		</div>
		<ul class="badges">
			<?php foreach ( $alph_ach as $alph_a ) : ?>
				<li class="badge-card<?php echo $alph_a[3] ? ' is-earned' : ''; ?>">
					<span class="badge-card__icon"><?php echo alph_icon( $alph_a[2], 20 ); // phpcs:ignore WordPress.Security.EscapeOutput ?></span>
					<span class="badge-card__text">
						<strong><?php echo esc_html( $alph_a[0] ); ?><?php echo $alph_a[3] ? alph_icon( 'check', 14 ) : ''; // phpcs:ignore WordPress.Security.EscapeOutput ?></strong>
						<small><?php echo esc_html( $alph_a[1] ); ?></small>
						<?php if ( ! $alph_a[3] ) : ?>
							<span class="minibar"><span style="width:<?php echo esc_attr( $alph_a[4] ); ?>%"></span></span>
						<?php endif; ?>
					</span>
				</li>
			<?php endforeach; ?>
		</ul>
	</section>
</div>
