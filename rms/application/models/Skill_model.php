<?php

class Skill_model extends CI_Model
{
  public $name;
  public $order;
  public $deleted;
  public $id_bu;

  public function getOne($id, $with_bu = false)
  {
    $this->db->select('s.*');
    $this->db->from('skills AS s');
    $this->db->where('s.id', $id);

    if ($with_bu)
    {
      $this->db->join('bus AS b', 'b.id = s.id_bu');
      $this->db->select('b.name AS bu_name');
    }

    return $this->db->get()->row();
  }

  public function alreadyExists($name, $id_bu)
  {
    $this->db->from('skills');
    $this->db->where('name', $name);
    $this->db->where('id_bu', $id_bu);

    return $this->db->count_all_results() > 0;
  }

  public function duplicate($id, $id_bu)
  {
    $this->db->trans_begin();

    // get original skill
    $original_skill = $this->getOne($id);

    // duplicate the skill
    $skill = clone $original_skill;
    unset($skill->id);
    $skill->deleted = 0;
    $skill->id_bu = $id_bu;
    $this->db->insert('skills', $skill);
    $skill->id = $this->db->insert_id();
    $this->db->trans_commit();

    // find items to know which (sub-)categories we need to duplicate
    $this->db->select('i.name, i.link, i.id_cat, i.id_sub_cat, i.order, cat.name AS cat_name, sub_cat.name AS sub_cat_name');
    $this->db->from('skills_item AS i');
    $this->db->join('skills_category AS cat', 'cat.id = i.id_cat');
    $this->db->join('skills_sub_category AS sub_cat', 'sub_cat.id = i.id_sub_cat');
    $this->db->where('i.id_skills', $id);
    $this->db->where('i.deleted', 0);
    $this->db->where('cat.id_bu', $original_skill->id_bu);
    $this->db->where('sub_cat.id_bu', $original_skill->id_bu);
    $this->db->where('cat.deleted', 0);
    $this->db->where('sub_cat.deleted', 0);
    $items_original = $this->db->get()->result();

    // map cat and sub_cat names
    $cat_ids = [];
    $cat_names = [];
    $sub_cat_ids = [];
    $sub_cat_names = [];
    foreach ($items_original as $item)
    {
      $cat_id = intval($item->id_cat);
      $sub_cat_id = intval($item->id_sub_cat);

      if (!in_array($cat_id, $cat_ids))
        array_push($cat_ids, $cat_id);

      if (!in_array($item->cat_name, $cat_names))
        array_push($cat_names, $item->cat_name);

      if (!in_array($sub_cat_id, $sub_cat_ids))
        array_push($sub_cat_ids, $sub_cat_id);

      if (!in_array($item->sub_cat_name, $sub_cat_names))
        array_push($sub_cat_names, $item->sub_cat_name);
    }

    // duplicate categories
    if (count($cat_ids))
    {
      $this->db->select('name, order');
      $this->db->from('skills_category');
      $this->db->where('id_bu', $original_skill->id_bu);
      $this->db->where('deleted', 0);
      $this->db->where_in('id', $cat_ids);
      $cats_originals = $this->db->get()->result();
      $cats_duplicates = [];
      foreach ($cats_originals as $cat)
      {
        array_push($cats_duplicates, [
          'name'    => $cat->name,
          'order'   => $cat->order,
          'deleted' => 0,
          'id_bu'   => $id_bu
        ]);
      }
      $this->db->insert_batch('skills_category', $cats_duplicates);
      $this->db->trans_commit();
    }

    // duplicate sub categories
    if (count($sub_cat_ids))
    {
      $this->db->select('name, order');
      $this->db->from('skills_sub_category');
      $this->db->where('id_bu', $original_skill->id_bu);
      $this->db->where('deleted', 0);
      $this->db->where_in('id', $sub_cat_ids);
      $sub_cats_originals = $this->db->get()->result();
      $sub_cats_duplicates = [];
      foreach ($sub_cats_originals as $sub_cat)
      {
        array_push($sub_cats_duplicates, [
          'name'    => $sub_cat->name,
          'order'   => $sub_cat->order,
          'deleted' => 0,
          'id_bu'   => $id_bu
        ]);
      }
      $this->db->insert_batch('skills_sub_category', $sub_cats_duplicates);
      $this->db->trans_commit();
    }

    // fetch new categories name/id mapping
    if (count($cat_names))
    {
      $this->db->select('id, name');
      $this->db->from('skills_category');
      $this->db->where('id_bu', $id_bu);
      $this->db->where_in('name', $cat_names);
      $result = $this->db->get()->result();
      $cats_new = [];
      foreach ($result as $cat)
      {
        $cats_new[$cat->name] = $cat->id;
      }
    }

    // fetch new sub-categories name/id mapping
    if (count($sub_cat_names))
    {
      $this->db->select('id, name');
      $this->db->from('skills_sub_category');
      $this->db->where('id_bu', $id_bu);
      $this->db->where_in('name', $sub_cat_names);
      $result = $this->db->get()->result();
      $sub_cats_new = [];
      foreach ($result as $sub_cat)
      {
        $sub_cats_new[$sub_cat->name] = $sub_cat->id;
      }
    }

    // duplicate items
    if (count($items_original))
    {
      $items_duplicates = [];
      foreach ($items_original as $item)
      {
        array_push($items_duplicates, [
          'id_skills'  => $skill->id,
          'name'       => $item->name,
          'link'       => $item->link,
          'order'      => $item->order,
          'deleted'    => 0,
          'id_cat'     => $cats_new[$item->cat_name],
          'id_sub_cat' => $sub_cats_new[$item->sub_cat_name]
        ]);
      }
      $this->db->insert_batch('skills_item', $items_duplicates);
    }

    $this->db->trans_complete();

    return $this->db->trans_status();
  }

  public function getCategories($id)
  {
    $this->db->select('name, order');
  }
}
